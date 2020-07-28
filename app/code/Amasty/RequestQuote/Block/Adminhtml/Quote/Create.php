<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote;

class Create extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_sessionQuote;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        array $data = []
    ) {
        $this->_sessionQuote = $sessionQuote;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'quote_id';
        $this->_controller = 'quote';
        $this->_mode = 'create';

        parent::_construct();

        $this->setId('sales_order_create');

        $customerId = $this->_sessionQuote->getCustomerId();
        $storeId = $this->_sessionQuote->getStoreId();

        $this->buttonList->update('save', 'label', __('Submit Quote'));
        $this->buttonList->update('save', 'onclick', 'quote.submit()');
        $this->buttonList->update('save', 'class', 'primary');
        // Temporary solution, unset button widget. Will have to wait till jQuery migration is complete
        $this->buttonList->update('save', 'data_attribute', []);

        $this->buttonList->update('save', 'id', 'submit_order_top_button');
        if ($customerId === null || !$storeId) {
            $this->buttonList->update('save', 'style', 'display:none');
        }

        $this->buttonList->update('back', 'id', 'back_order_top_button');
        $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $this->getBackUrl() . '\')');

        $this->buttonList->update('reset', 'id', 'reset_order_top_button');

        if ($customerId === null) {
            $this->buttonList->update('reset', 'style', 'display:none');
        } else {
            $this->buttonList->update('back', 'style', 'display:none');
        }

        $confirm = __('Are you sure you want to cancel this quote?');
        $this->buttonList->update('reset', 'label', __('Cancel'));
        $this->buttonList->update('reset', 'class', 'cancel');
        $this->buttonList->update(
            'reset',
            'onclick',
            'deleteConfirm(\'' . $confirm . '\', \'' . $this->getCancelUrl() . '\')'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $pageTitle = $this->getLayout()->createBlock(
            \Amasty\RequestQuote\Block\Adminhtml\Quote\Create\Header::class
        )->toHtml();
        if (is_object($this->getLayout()->getBlock('page.title'))) {
            $this->getLayout()->getBlock('page.title')->setPageTitle($pageTitle);
        }
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getHeaderHtml()
    {
        $out = '<div id="quote-header">' . $this->getLayout()->createBlock(
                \Amasty\RequestQuote\Block\Adminhtml\Quote\Create\Header::class
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
    protected function _getSession()
    {
        return $this->_sessionQuote;
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        if ($this->_sessionQuote->getOrder()->getId()) {
            $url = $this->getUrl('amasty_quote/quote/view', ['quote_id' => $this->_sessionQuote->getOrder()->getId()]);
        } else {
            $url = $this->getUrl('amasty_quote/*/cancel');
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('amasty_quote/' . $this->_controller . '/');
    }
}
