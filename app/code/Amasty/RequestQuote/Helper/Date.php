<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Date extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        Context $context
    ) {
        parent::__construct($context);
        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
    }

    /**
     * @param int $days
     *
     * @return string
     */
    public function increaseDays($days)
    {
        return $this->dateTime->gmtDate(null, strtotime("+$days days"));
    }

    /**
     * @param $date
     *
     * @return string
     */
    public function formatDate($date)
    {
        return $this->localeDate->formatDateTime(
            new \DateTime($date),
            \IntlDateFormatter::MEDIUM,
            true
        );
    }
}
