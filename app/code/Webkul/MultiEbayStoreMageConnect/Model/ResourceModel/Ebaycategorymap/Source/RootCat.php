<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategorymap\Source;

class RootCat
{
    protected $_categoryFactory;
    protected $_ebaycategoryFactory;
    protected $_resourceConnection;
    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\EbaycategoryFactory $ebaycategoryFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_ebaycategoryFactory = $ebaycategoryFactory;
        $this->_resourceConnection = $resourceConnection;
    }

    /**
     * Return options array.
     * @param int $store
     * @return array
     */
    public function tomageCatArray($store = null)
    {
        $categoriesArr[] = ['value' => '','label' => 'Select Store Category'];
        $categories = $this->_categoryFactory->create()->getCollection()->addFieldToFilter('parent_id', ['eq' => 2]);

        foreach ($categories as $category) {
            $category = $this->_categoryFactory->create()->load($category->getEntityId());
            $categoriesArr[] = ['value' => $category->getEntityId(),'label' => $category->getName()];
        }

        return $categoriesArr;
    }

    /**
     * Return options array.
     * @param int $store
     * @return array
     */
    public function toebayCatArray($store = null)
    {
        $categoriesArr[] = ['value' => '','label' => 'Select eBay Category'];
        $write = $this->_resourceConnection->getConnection();
        $wkEbayCategories = $this->_resourceConnection->getTableName('wk_multiebay_categories');
        $querydata = $write->query(
            "SELECT `ebay_cat_id`,`ebay_cat_name` FROM `$wkEbayCategories` WHERE `ebay_cat_parentid`=`ebay_cat_id`"
        );
        // echo "querydata<pre>"; print_r($querydata);die;
        foreach ($querydata as $category) {
            $categoriesArr[] = ['value' => $category['ebay_cat_id'],'label' => $category['ebay_cat_name']];
        }

        return $categoriesArr;
    }
    /**
     * Get options in "key-value" format.
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
