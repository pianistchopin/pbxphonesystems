<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use \Magento\Framework\Exception\LocalizedException;
use Ebay;

class AdminSystemConfigChangedObserver implements ObserverInterface
{
    const _WSDL_VERSION_ = 933;

    /**
     *
     * @param RequestInterface $requestInterface
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper
     */
    public function __construct(
        RequestInterface $requestInterface,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper
    ) {
    
        $this->_request = $requestInterface;
        $this->helper = $helper;
    }

    /**
     * admin_system_config_changed_section_mppushnotification event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data =  $this->_request->getParams();
        $fields = $data['groups']['ebay_event']['fields'];
        try {
            $client = $this->helper->getEbayAPI();
            if (isset($fields['notification_status']['value']) && $eventList = $fields['notification_status']['value']) {
                $this->helper->enableEventNotification($client, 0, $eventList);
            } else {
                $this->helper->disableEventNotification($client, 0);
            }
        } catch (\Exception $e) {
                $e = $e->getMessage();
        }
    }
}
