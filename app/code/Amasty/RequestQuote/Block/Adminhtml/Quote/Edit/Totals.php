<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Totals extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\AbstractEdit
{
    /**
     * @var array
     */
    protected $totalRenderers;

    /**
     * @var string
     */
    protected $defaultRenderer = \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Totals\DefaultTotals::class;

    /**
     * @var \Magento\Sales\Helper\Data
     */
    protected $salesData = null;

    /**
     * @var \Magento\Sales\Model\Config
     */
    protected $salesConfig;

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $taxConfig;

    /**
     * @var array
     */
    protected $allowedTotals = ['subtotal', 'tax', 'shipping', 'grand_total'];

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $sessionQuote,
        \Amasty\RequestQuote\Model\Quote\Backend\Edit $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = []
    ) {
        $this->salesData = $salesData;
        $this->salesConfig = $salesConfig;
        $this->taxConfig = $taxConfig;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('amasty_quote_edit_totals');
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
        return array_intersect_key($totals, array_flip($this->allowedTotals));
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
        if (!$block) {
            $configRenderer = $this->getTotalRenderer($code);
            if (empty($configRenderer)) {
                $block = $this->defaultRenderer;
            } else {
                $block = $configRenderer;
            }

            $block = $this->getLayout()->createBlock($block, $blockName);
        }
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
        $this->getQuote()->setTotalsCollectedFlag(true);
        foreach ($this->getTotals() as $total) {
            if ($total->getArea() != $area && $area != -1) {
                continue;
            }
            $html .= $this->renderTotal($total, $area, $colspan);
        }
        return $html;
    }

    /**
     * @param $code
     * @return string
     */
    public function getTotalRenderer($code)
    {
        switch ($code) {
            case 'subtotal':
                $configRenderer = Totals\Subtotal::class;
                break;
            case 'tax':
                $configRenderer = Totals\Tax::class;
                break;
            case 'shipping':
                $configRenderer = Totals\Shipping::class;
                break;
            case 'grand_total':
                $configRenderer = Totals\Grandtotal::class;
                break;
            default:
                $configRenderer = $this->salesConfig->getTotalsRenderer('quote', 'totals', $code);
        }
        return $configRenderer;
    }
}
