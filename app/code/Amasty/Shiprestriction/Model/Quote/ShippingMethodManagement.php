<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Model\Quote;

use Magento\Quote\Model\Quote;

/**
 * Class ShippingMethodManagement
 */
class ShippingMethodManagement extends \Magento\Quote\Model\ShippingMethodManagement
{
    public function estimateByAddressId($cartId, $addressId)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        $address = $this->addressRepository->getById($addressId);

        $additionalData = [
            'street' => $address->getStreet(),
            'city' => $address->getCity(),
        ];

        return $this->getEstimatedRates(
            $quote,
            $address->getCountryId(),
            $address->getPostcode(),
            $address->getRegionId(),
            $address->getRegion(),
            $additionalData
        );
    }

    /**
     * {@inheritDoc}
     */
    public function estimateByAddress($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        $addData = [];

        foreach ($address->getCustomAttributes() as $customAttribute) {
            $addData[$customAttribute->getAttributeCode()] = $customAttribute->getValue();
        }

        return $this->getEstimatedRates(
            $quote,
            $address->getCountryId(),
            $address->getPostcode(),
            $address->getRegionId(),
            $address->getRegion(),
            $addData
        );
    }

    /**
     * Get estimated rates
     *
     * @param Quote $quote
     * @param int $country
     * @param string $postcode
     * @param int $regionId
     * @param string $region
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     */
    protected function getEstimatedRates(Quote $quote, $country, $postcode, $regionId, $region, $addData = [])
    {
        $output = [];
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress
            ->setCountryId($country)
            ->setPostcode($postcode)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->addData($addData);

        $shippingAddress->setCollectShippingRates(true);
        $this->totalsCollector->collectAddressTotals($quote, $shippingAddress);
        $shippingRates = $shippingAddress->getGroupedAllShippingRates();

        foreach ($shippingRates as $carrierRates) {
            foreach ($carrierRates as $rate) {
                $output[] = $this->converter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
            }
        }

        return $output;
    }
}
