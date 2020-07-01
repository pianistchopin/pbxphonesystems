<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Product;

class Profiler extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Webkul\Ebaymagentoconnect\Helper\Data
     */
    private $_helperData;

    /**
     * @param \Magento\Backend\Block\Widget\Context  $context
     * @param \Webkul\Ebaymagentoconnect\Helper\Data $helperData
     * @param array                                  $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helperData = $helperData;
    }

    /**
     * For get total imported product count.
     * @return int
     */
    public function getImportedProduct()
    {
        $ruleId = $this->getRequest()->getParam('id');
        return $this->_helperData->getTotalImportedCount('product', $ruleId);
    }
}
