<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Account\Quote;

use Amasty\RequestQuote\Model\Source\Status;

class Totals extends \Magento\Checkout\Block\Cart\Totals
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var array
     */
    private $allowedCodes = [
        'subtotal',
        'tax',
        'weee_tax',
        'grand_total'
    ];

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Framework\Registry $registry,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $salesConfig, $layoutProcessors, $data);
        $this->registry = $registry;
        if ($this->getQuote()->getStatus() == Status::COMPLETE) {
            $this->allowedCodes[] = 'shipping';
        }
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote
     */
    public function getQuote()
    {
        return $this->registry->registry('amasty_quote');
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
