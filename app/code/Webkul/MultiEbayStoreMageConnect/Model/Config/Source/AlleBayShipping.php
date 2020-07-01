<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model\Config\Source;

class AlleBayShipping implements \Magento\Framework\Option\ArrayInterface
{
      /**
       * @param \Magento\Backend\App\Action\Context $context,
       * @param EbayConnectHelperData $ebayConnectHelperData,
       * @param EbaycategoryFactory $ebayCategory,
       * @param EbaycategorymapFactory $ebaycategoryMap,
       * @param CategoriesspecificationFactory $categoriesSpecification
       */
    public function __construct(
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper
    ) {
        $this->helper =  $helper;
    }

    /**
     * Return options array.
     *
     * @param int $store
     *
     * @return array
     */
    public function toOptionArray($store = null)
    {
        $client = $this->helper->getEbayAPI();
        if ($client) {
            /**for get ebay category Detail*/
            try {
                $params = [
                        'Version' => 891,
                        'DetailName' => 'ShippingServiceDetails',
                        'WarningLevel' => 'High'
                    ];
                $shippingArr = [];
                $results = $client->GeteBayDetails($params);
                if ($results->Ack == 'Success' && isset($results->ShippingServiceDetails)) {
                    $shippingArr[] = ['value' => '', 'label' => __('Select eBay Shipping')];
                    foreach ($results->ShippingServiceDetails as $key => $shippingService) {
                        $shippingArr[] = [
                            'value' => $shippingService->ShippingService,
                            'label' => $shippingService->Description. ' ('.$shippingService->ShippingCategory.')'
                        ];
                    }
                }
            } catch (\Exception $e) {
                $shippingArr = [];
            }
        }

        if (empty($shippingArr)) {
            $shippingArr = [
                ['value' => '', 'label' => __('Select eBay Shipping')],
                ['value' => 'Other', 'label' => 'Economy Shipping'],
                ['value' => 'DE_Pickup', 'label' => 'Pick up (Germany)'],
                ['value' => 'UK_OtherCourier', 'label' => 'Other courier (UK)']
            ];
        }
        return $shippingArr;
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray()
    {
        $optionList = $this->toOptionArray();
        $optionArray = [];
        foreach ($optionList as $option) {
            $optionArray[$option['value']] = $option['label'];
        }

        return $optionArray;
    }
}
