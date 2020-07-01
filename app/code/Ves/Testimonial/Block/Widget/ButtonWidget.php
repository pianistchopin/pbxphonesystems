<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Testimonial
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Testimonial\Block\Widget;

class ButtonWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \Ves\Testimonial\Helper\Data
     */
    protected $_helper;
	/**
     * @param \Magento\Catalog\Block\Product\Context $context     
     * @param \Magento\Framework\Url\Helper\Data     $urlHelper   
     * @param array                                  $data        
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Ves\Testimonial\Helper\Data $_helper,
        array $data = []
        ) {
        $this->_helper = $_helper;
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
    }

    public function _toHtml()
    {
        $enable = $this->_helper->getConfig('general/enable');
        if(!$enable) return;
        $template = 'Ves_Testimonial::widget/button_widget.phtml';
        $this->setTemplate($template);
        return parent::_toHtml();
    }
    public function getConfig($key, $default = '')
    {
        if($this->hasData($key) && $this->getData($key))
        {
            return $this->getData($key);
        }
        return $default;
    }
}