<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Totals extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Create\AbstractCreate
{
    /**
     * @var array
     */
    private $allowedTotals = [
        'subtotal',
        'shipping',
        'tax',
        'grand_total'
    ];

    /**
     * @var array
     */
    protected $_totalRenderers;

    /**
     * @var string
     */
    protected $_defaultRenderer = \Amasty\RequestQuote\Block\Adminhtml\Quote\Create\Totals\DefaultTotals::class;

    /**
     * @var \Magento\Sales\Helper\Data
     */
    protected $_salesData = null;

    /**
     * @var \Magento\Sales\Model\Config
     */
    protected $_salesConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $sessionQuote,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Sales\Model\Config $salesConfig,
        array $data = []
    ) {
        $this->_salesData = $salesData;
        $this->_salesConfig = $salesConfig;
        parent::__construct($context, $sessionQuote, $priceCurrency, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_totals');
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        $this->getQuote()->collectTotals();
        if ($this->getQuote()->isVirtual()) {
            $totals = $this->getQuote()->getBillingAddress()->getTotals();
        } else {
            $totals = $this->getQuote()->getShippingAddress()->getTotals();
        }
        foreach ($totals as $code => $total) {
            if (!in_array($code, $this->allowedTotals)) {
                unset($totals[$code]);
            }
        }

        return $totals;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Quote Totals');
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-money';
    }

    /**
     * @param string $code
     * @return bool|\Magento\Framework\View\Element\BlockInterface
     */
    protected function _getTotalRenderer($code)
    {
        $blockName = $code . '_total_renderer';
        $block = $this->getLayout()->getBlock($blockName);

        $block->setTotals($this->getTotals());

        return $block;
    }

    /**
     * @param \Magento\Framework\DataObject $total
     * @param string|null $area
     * @param int $colspan
     * @return mixed
     */
    public function renderTotal($total, $area = null, $colspan = 1)
    {
        return $this->_getTotalRenderer(
            $total->getCode()
        )->setTotal(
            $total
        )->setColspan(
            $colspan
        )->setRenderingArea(
            $area === null ? -1 : $area
        )->toHtml();
    }

    /**
     * @param null $area
     * @param int $colspan
     * @return string
     */
    public function renderTotals($area = null, $colspan = 1)
    {
        $html = '';
        foreach ($this->getTotals() as $total) {
            if ($total->getArea() != $area && $area != -1) {
                continue;
            }
            $html .= $this->renderTotal($total, $area, $colspan);
        }
        return $html;
    }

    /**
     * @return bool
     */
    public function canSendNewOrderConfirmationEmail()
    {
        return $this->_salesData->canSendNewOrderConfirmationEmail($this->getQuote()->getStoreId());
    }

    /**
     * @return bool
     */
    public function getNoteNotify()
    {
        $notify = $this->getQuote()->getCustomerNoteNotify();
        if ($notify === null || $notify) {
            return true;
        }
        return false;
    }
}
