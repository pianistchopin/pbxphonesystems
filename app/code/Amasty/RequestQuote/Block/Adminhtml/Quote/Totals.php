<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote;

class Totals extends \Magento\Sales\Block\Adminhtml\Totals
{
    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    private $quoteSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $quoteSession,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->quoteSession = $quoteSession;
    }

    /**
     * @return \Amasty\RequestQuote\Api\Data\QuoteInterface
     */
    public function getSource()
    {
        return $this->quoteSession->getQuote();
    }

    /**
     * @param \Magento\Framework\DataObject $total
     * @return string
     */
    public function formatValue($total)
    {
        if (!$total->getIsFormated()) {
            return $this->displayPrices($this->getSource(), $total->getBaseValue(), $total->getValue());
        }
        return $total->getValue();
    }

    /**
     * @param   \Magento\Framework\DataObject $dataObject
     * @param   float $basePrice
     * @param   float $price
     * @param   bool $strong
     * @param   string $separator
     * @return  string
     */
    public function displayPrices($quote, $basePrice, $price, $strong = false, $separator = '<br/>')
    {
        if ($quote && $quote->isCurrencyDifferent()) {
            $res = '<strong>';
            $res .= $quote->formatBasePrice($basePrice);
            $res .= '</strong>' . $separator;
            $res .= '[' . $quote->formatPrice($price) . ']';
        } elseif ($quote) {
            $res = $quote->formatPrice($price);
            if ($strong) {
                $res = '<strong>' . $res . '</strong>';
            }
        } else {
            $res = $quote->formatPrice($price);
            if ($strong) {
                $res = '<strong>' . $res . '</strong>';
            }
        }
        return $res;
    }

    /**
     * @return $this
     */
    protected function _initTotals()
    {
        $this->_totals = [];
        $this->_totals['subtotal'] = new \Magento\Framework\DataObject(
            [
                'code' => 'subtotal',
                'value' => $this->getSource()->getSubtotal(),
                'base_value' => $this->getSource()->getBaseSubtotal(),
                'label' => __('Subtotal'),
            ]
        );

        $this->_totals['grand_total'] = new \Magento\Framework\DataObject(
            [
                'code' => 'grand_total',
                'strong' => true,
                'value' => $this->getSource()->getGrandTotal(),
                'base_value' => $this->getSource()->getBaseGrandTotal(),
                'label' => __('Grand Total'),
                'area' => 'footer',
            ]
        );
        return $this;
    }
}
