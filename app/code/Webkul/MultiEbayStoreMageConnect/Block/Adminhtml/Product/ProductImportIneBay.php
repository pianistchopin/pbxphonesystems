<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class ProductImportIneBay extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CollectionFactory
     */
    private $_productCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param CollectionFactory                     $productCollectionFactory
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        CollectionFactory $productCollectionFactory,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->_productCollectionFactory = $productCollectionFactory;
    }

    /**
     * For get selected mage product count.
     * @return int
     */
    public function getProductsListForImportIneBay()
    {
        $params = $this->getRequest()->getParams();
        $mageProCollection = $this->_productCollectionFactory
                            ->create()
                            ->addFieldToFilter(
                                'entity_id',
                                ['in'=>$params['mageProEntityIds']]
                            )->getColumnValues('entity_id');
        return $mageProCollection;
    }

    /**
     * get default getConfigValue
     *
     * @return void
     */
    public function getConfigValue($path)
    {
        return $this->helper->getConfigValue($path);
    }
}
