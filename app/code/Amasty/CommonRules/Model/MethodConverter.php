<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model;

/**
 * MethodConverter
 */
class MethodConverter
{
    /**
     * @var \Magento\Shipping\Model\Config
     */
    private $shippingConfig;

    /**
     * @var array
     */
    private $methods = [];

    public function __construct(
        \Magento\Shipping\Model\Config $shippingConfig
    ) {
        $this->shippingConfig = $shippingConfig;
    }

    /**
     * Convert comma-separated string of shipping methods codes to string with labels of that methods
     *
     * @param string $methodsStr
     *
     * @return string
     */
    public function convert($methodsStr)
    {
        $methods = $this->getCarrierMethods();
        $result = [];
        $currentMethods = explode(",", $methodsStr);

        foreach ($currentMethods as $currentMethod) {
            if (!empty($currentMethod) && array_key_exists($currentMethod, $methods)) {
                $result[] = $methods[$currentMethod];
            }
        }

        return implode("<br>", $result); // @codingStandardsIgnoreLine
    }

    /**
     * Return array of shipping method codes, which label contains $likeValue.
     *
     * @param string $likeValue
     *
     * @return array|string
     */
    public function getCodes($likeValue)
    {
        $likeValue = trim(str_replace('%', '', $likeValue));

        if (stripos('Any', $likeValue) !== false) {
            return '';
        }

        $methods = $this->getCarrierMethods();

        return array_keys(array_filter($methods, function ($var) use ($likeValue) {
            return stripos($var, $likeValue) !== false;
        }));
    }

    /**
     * Return all shipping methods as array.
     * Format like: method_code => [carrier_code] + method_label
     *
     * @return array
     */
    public function getCarrierMethods()
    {
        if (!$this->methods) {
            $methods = [];
            $carriers = $this->shippingConfig->getAllCarriers();

            /** @var \Magento\Shipping\Model\Carrier\CarrierInterface $carrierModel */
            foreach ($carriers as $carrierCode => $carrierModel) {
                $carrierMethods = $carrierModel->getAllowedMethods();

                if (!$carrierMethods) {
                    continue;
                }

                foreach ($carrierMethods as $methodCode => $methodTitle) {
                    if (strpos($carrierCode, '_') === false) {
                        $methods[$carrierCode . '_' . $methodCode] = '[' . $carrierCode . '] ' . $methodTitle;
                    } else {
                        $methods[$carrierCode] = '[' . $carrierCode . '] ' . $methodTitle;
                    }
                }
            }

            $this->methods = $methods;
        }

        return $this->methods;
    }
}
