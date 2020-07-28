<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Account\Quote;

use Amasty\Base\Model\Serializer;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Amasty\RequestQuote\Model\Source\Status;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order;

class Items extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * @var int
     */
    private $itemsPerPage;

    /**
     * @var \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    private $itemCollection;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /** @var Order|null */
    private $order;

    public function __construct(
        TemplateContext $context,
        Registry $registry,
        Serializer $serializer,
        DataObjectFactory $dataObjectFactory,
        OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->serializer = $serializer;
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($context, $data);
        $this->orderFactory = $orderFactory;
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('amasty_quote');
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->itemsPerPage = (int)$this->_scopeConfig->getValue('sales/orders/items_per_page');

        $this->itemCollection = $this->getQuote()->getItemsCollection();
        $this->itemCollection->addFieldToFilter('parent_item_id', ['null' => 'parent_item_id']);
        /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
        $pagerBlock = $this->getChildBlock('amasty_quote_item_pager');
        if ($pagerBlock) {
            $pagerBlock->setLimit($this->itemsPerPage);
            //here pager updates collection parameters
            $pagerBlock->setCollection($this->itemCollection);
            $pagerBlock->setAvailableLimit([$this->itemsPerPage]);
            $pagerBlock->setShowAmounts($this->isPagerDisplayed());
        }

        foreach ($this->getItems() as $item) {
            if($itemNotes = $this->serializer->unserialize($item->getAdditionalData())) {
                $itemNotes = $this->dataObjectFactory->create(['data' => $itemNotes]);
                $item->setNotes($itemNotes);
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * @return bool
     */
    public function isPagerDisplayed()
    {
        $pagerBlock = $this->getChildBlock('amasty_quote_item_pager');
        return $pagerBlock && ($this->itemCollection->getSize() > $this->itemsPerPage);
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
        $pagerBlock = $this->getChildBlock('amasty_quote_item_pager');
        return $pagerBlock ? $pagerBlock->toHtml() : '';
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getItems()
    {
        return $this->itemCollection->getItems();
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getMoveUrl($params = [])
    {
        return $this->_urlBuilder->getUrl('amasty_quote/move/inCart', array_merge([
            'quote_id' => $this->getQuote()->getId()
        ], $params));
    }

    /**
     * @return bool
     */
    public function isMoveShowed()
    {
        return $this->getQuote()->getStatus() == Status::APPROVED;
    }

    /**
     * @return bool
     */
    public function isQuoteComplete()
    {
        return $this->getQuote()->getStatus() == Status::COMPLETE;
    }



    /**
     * @return Order|null
     */
    public function getOrder()
    {
        if ($this->order === null && $this->getQuote()->getStatus() == Status::COMPLETE) {
            /** @var Order $order */
            $this->order = $this->orderFactory->create();
            $this->order->loadByIncrementId($this->getQuote()->getReservedOrderId());
        }

        return $this->order;
    }

    /**
     * @return string
     */
    public function getOrderViewUrl()
    {
        return $this->getUrl('sales/order/view', ['order_id' => $this->getOrder()->getId()]);
    }
}
