<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Email;

use Amasty\RequestQuote\Api\Data\QuoteInterface;
use Amasty\RequestQuote\Helper\Data;
use Amasty\RequestQuote\Block\Email\Grid\Quote as QuoteGrid;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\App\State;
use Magento\Store\Model\Store;
use Magento\Framework\App\Area;
use Psr\Log\LoggerInterface;

/**
 * Class AdminNotification
 */
class AdminNotification
{
    const SENT = 1;
    const NOT_SENT = 0;

    /**
     * @var Sender
     */
    private $emailSender;

    /**
     * @var QuoteGrid
     */
    private $quoteGrid;

    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var Data
     */
    private $configHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Data $configHelper,
        Sender $emailSender,
        QuoteGrid $quoteGrid,
        Emulation $appEmulation,
        State $appState,
        LoggerInterface $logger
    ) {
        $this->emailSender = $emailSender;
        $this->quoteGrid = $quoteGrid;
        $this->appEmulation = $appEmulation;
        $this->appState = $appState;
        $this->configHelper = $configHelper;
        $this->logger = $logger;
    }

    public function notify()
    {
        if ($this->configHelper->isAdminNotificationsByCron()) {
            $this->sendNotification();
        }
    }

    /**
     * @param null|array $ids
     */
    public function sendNotification($ids = null)
    {
        try {
            $this->appEmulation->startEnvironmentEmulation(Store::DEFAULT_STORE_ID);
            $this->quoteGrid->addIdFilter($ids);
            $quoteGrid = $this->appState->emulateAreaCode(
                Area::AREA_FRONTEND,
                [$this->quoteGrid, 'toHtml']
            );
            $this->appEmulation->stopEnvironmentEmulation();
            $this->emailSender->sendEmail(Data::CONFIG_PATH_ADMIN_NOTIFY_EMAIL, null, [
                'quoteGrid' => $quoteGrid
            ]);
            $this->quoteGrid->getQuoteCollection()->setDataToAll(QuoteInterface::ADMIN_NOTIFICATION_SEND, self::SENT);
            $this->quoteGrid->getQuoteCollection()->save();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
