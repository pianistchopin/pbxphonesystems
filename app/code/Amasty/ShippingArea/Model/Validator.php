<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Model;

use Amasty\ShippingArea\Model\System\ConditionOptionProvider;

class Validator
{
    /**
     * @param Area $area
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute($area, $address)
    {
        $result = true;
        
        if ($area->getCountryCondition() && is_array($area->getCountrySet())) {
            $result = in_array($address->getCountry(), $area->getCountrySet());
            
            if ($area->getCountryCondition() == ConditionOptionProvider::CONDITION_EXCLUDE) {
                $result = !$result;
            }
            
            if (!$result) {
                return $result;
            }
        }

        if ($area->getStateCondition()) {
            if ($area->getStateSetListing() && is_array($area->getStateSetListing())) {
                $result = in_array($address->getRegionId(), $area->getStateSetListing());
            } else {
                $result = $this->compareValues($address->getRegionCode(), $area->getStateSet());
            }

            if ($area->getStateCondition() == ConditionOptionProvider::CONDITION_EXCLUDE) {
                $result = !$result;
            }
            
            if (!$result) {
                return $result;
            }
        }

        if ($area->getCityCondition() == ConditionOptionProvider::CONDITION_EXCLUDE
            && $area->getPostcodeCondition() == ConditionOptionProvider::CONDITION_EXCLUDE
        ) {
            if ($this->compareValues($address->getCity(), $area->getCitySet())
                && !$this->comparePostcode($area, $address->getPostcode())
            ) {
                return false;
            }
        } else {
            if ($area->getCityCondition()) {
                $result = $this->compareValues($address->getCity(), $area->getCitySet());
                
                if ($area->getCityCondition() == ConditionOptionProvider::CONDITION_EXCLUDE) {
                    $result = !$result;
                }
                
                if (!$result) {
                    return $result;
                }
            }

            $result = $this->validatePostcode($area, $address->getPostcode());
            
            if (!$result) {
                return $result;
            }
        }

        if ($area->getAddressCondition()) {
            $result = false;
            $inputStreet = $address->getStreet();
            
            foreach ($area->getStreetArray() as $streetLine) {
                foreach ($inputStreet as $inputStreetLine) {
                    if (stripos($inputStreetLine, $streetLine) !== false) {
                        $result = true;
                        break 2;
                    }
                }
            }
            
            if ($area->getAddressCondition() == ConditionOptionProvider::CONDITION_EXCLUDE) {
                $result = !$result;
            }
            
            if (!$result) {
                return $result;
            }
        }

        return $result;
    }

    /**
     * @param Area $area
     * @param string $postcode
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validatePostcode($area, $postcode)
    {
        $result = true;

        if ($area->getPostcodeCondition()) {
            $result = $this->comparePostcode($area, $postcode);
            
            if ($area->getPostcodeCondition() == ConditionOptionProvider::CONDITION_EXCLUDE) {
                $result = !$result;
            }
        }

        return $result;
    }

    /**
     * @param Area $area
     * @param string $postcode
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function comparePostcode($area, $postcode)
    {
        $postcodeSet = $area->getPostcodeSet();
        $postcodeData = $this->extractDataFromZip($postcode);
        
        if (is_array($postcodeSet)) {
            foreach ($postcodeSet as $zipRow) {
                
                if (empty($zipRow['zip_to'])) {
                    if ($postcode == $zipRow['zip_from']) {
                        return true;
                    }
                    
                    continue;
                }
                $zipFrom = $this->extractDataFromZip($zipRow['zip_from']);
                $zipTo = $this->extractDataFromZip($zipRow['zip_to']);
                
                if ($zipFrom['area'] && $postcodeData['area'] !== $zipFrom['area']) {
                    continue;
                }
                
                if ($zipFrom['district'] <= $postcodeData['district']
                    && $zipTo['district'] >= $postcodeData['district']
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Case and type insensitive comparison of values
     *
     * @param string $validatedValue
     * @param string $value
     *
     * @return bool
     */
    protected function compareValues($validatedValue, $value)
    {
        $validatePattern = preg_quote($validatedValue, '~');
        $value = str_replace(["\r\n", "\r"], "\n", $value);

        return (bool)preg_match('~^' . $validatePattern . '$~miu', $value);
    }

    /**
     * @param string $zip
     * @return array('area' => string, 'district' => int)
     */
    protected function extractDataFromZip($zip)
    {
        $dataZip = ['area' => '', 'district' => ''];

        if (!empty($zip)) {
            $zipSpell = str_split($zip);
            
            foreach ($zipSpell as $element) {
                if ($element === ' ') {
                    break;
                }
                
                if (is_numeric($element)) {
                    $dataZip['district'] = $dataZip['district'] . $element;
                } elseif (empty($dataZip['district'])) {
                    $dataZip['area'] = $dataZip['area'] . $element;
                }
            }
        }

        return $dataZip;
    }
}
