<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Product;

class ImportButton extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Helper\Data
     */
    private $helper;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
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
