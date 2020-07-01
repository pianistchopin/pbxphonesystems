<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model\Config\Source;

class AllStoreList implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManger;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManger
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManger
    ) {
        $this->_storeManger = $storeManger;
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
        $stores = $this->_storeManger->getStores();
        foreach ($stores as $store) {
            $optionArray[$store->getId()] = $store->getName();
        }

        return $optionArray;
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->toOptionArray();
    }
}
