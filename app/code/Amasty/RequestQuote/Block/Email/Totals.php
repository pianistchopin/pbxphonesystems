<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Email;

class Totals extends \Magento\Checkout\Block\Cart\Totals
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Amasty\RequestQuote\Model\QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var \Amasty\RequestQuote\Model\Quote
     */
    private $quote = null;

    /**
     * @var array
     */
    private $allowedCodes = [
        'subtotal',
        'tax',
        'grand_total'
    ];

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Amasty\RequestQuote\Model\Quote\Session $checkoutSession,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Framework\Registry $registry,
        \Amasty\RequestQuote\Model\QuoteRepository $quoteRepository,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $salesConfig, $layoutProcessors, $data);
        $this->registry = $registry;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote|\Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote|mixed
     */
    public function getQuote()
    {
        if ($this->quote === null) {
            $this->quote = $this->quoteRepository->get($this->getData('quote')->getId());
        }

        return $this->quote;
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        $totals = parent::getTotals();
        foreach ($totals as $code => $total) {
            if (!in_array($code, $this->allowedCodes)) {
                unset($totals[$code]);
                continue;
            }
            $total['label'] = $total['title'];
        }

        return $totals;
    }

    /**
     * @param \Magento\Framework\DataObject $total
     *
     * @return string
     */
    public function formatValue($total)
    {
        if (!$total->getIsFormated()) {
            $value = $this->getQuote()->formatPrice($total->getValue());
        } else {
            $value = $total->getValue();
        }

        return $value;
    }
}
