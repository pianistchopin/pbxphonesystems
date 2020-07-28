<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Sales\Block\Adminhtml\Order;

use Amasty\RequestQuote\Controller\Adminhtml\Quote\Create\FromOrder;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\View;

/**
 * Class ViewPlugin
 */
class ViewPlugin
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        AuthorizationInterface $authorization,
        UrlInterface $urlBuilder
    ) {
        $this->authorization = $authorization;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param View $subject
     * @param LayoutInterface $layout
     *
     * @return array
     */
    public function beforeSetLayout(View $subject, LayoutInterface $layout)
    {
        if ($this->authorization->isAllowed(FromOrder::ADMIN_RESOURCE)) {
            $subject->addButton('clone_as_quote', [
                'label' => __('Clone as Quote'),
                'class' => 'clone',
                'id' => 'clone-as-quote',
                'onclick' => 'setLocation(\'' . $this->getCloneUrl($subject->getOrderId()) . '\')'
            ]);
        }

        return [$layout];
    }

    /**
     * @param int $orderId
     * @return string
     */
    private function getCloneUrl($orderId)
    {
        return $this->urlBuilder->getUrl(
            'amasty_quote/quote_create/fromOrder',
            ['order_id' => $orderId]
        );
    }
}
