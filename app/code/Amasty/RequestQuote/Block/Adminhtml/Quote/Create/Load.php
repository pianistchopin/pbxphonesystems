<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create;

class Load extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\View\Helper\Js
     */
    private $jsHelper = null;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\View\Helper\Js $jsHelper,
        array $data = []
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->jsHelper = $jsHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $result = [];
        foreach ($this->getChildNames() as $name) {
            $result[$name] = $this->getLayout()->renderElement($name);
        }
        $json = $this->jsonEncoder->encode($result);
        $jsVarname = $this->getRequest()->getParam('as_js_varname');
        if ($jsVarname) {
            return $this->jsHelper->getScript("var {$jsVarname} = {$json}");
        }
        
        return $json;
    }
}
