<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */

namespace Amasty\RequestQuote\Block\Adminhtml\Quote;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Amasty_RequestQuote';

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Session|\Magento\Backend\Model\Session\Quote
     */
    protected $sessionQuote;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $sessionQuote,
        array $data = []
    ) {
        $this->sessionQuote = $sessionQuote;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'quote_id';
        $this->_controller = 'adminhtml_quote';
        $this->_mode = 'edit';

        parent::_construct();

        $this->setId('amasty_quote_edit');

        $this->buttonList->update('save', 'label', __('Save Quote'));
        $this->buttonList->update('save', 'onclick', 'quote.submit()');
        $this->buttonList->update('save', 'class', 'primary');
        $this->buttonList->update('save', 'data_attribute', []);
        $this->buttonList->update('save', 'id', 'save_quote_top_button');
        $this->buttonList->update('back', 'id', 'back_quote_top_button');
        $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $this->getBackUrl() . '\')');
        $this->removeButton('delete');
        $this->removeButton('reset');
    }

    /**
     * @return string
     */
    public function getHeaderHtml()
    {
        $out = '<div id="quote-header">' . $this->getLayout()->createBlock(
            \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Header::class
        )->toHtml() . '</div>';
        return $out;
    }

    /**
     * @return string
     */
    public function getHeaderWidth()
    {
        return 'width: 70%;';
    }

    /**
     * @return \Magento\Backend\Model\Session\Quote
     */
    protected function getSession()
    {
        return $this->sessionQuote;
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        if ($this->getSession()->getQuote()->getId()) {
            $url = $this->getUrl(
                'amasty_quote/quote/view',
                ['quote_id' => $this->getSession()->getQuote()->getId()]
            );
        } else {
            $url = $this->getUrl('amasty_quote/quote/index');
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            'amasty_quote/quote/view',
            ['quote_id' => $this->getSession()->getParentQuote()->getId()]
        );
    }
}
