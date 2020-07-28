<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Cart;

class Grid extends \Amasty\RequestQuote\Block\Cart
{
    /**
     * Config settings path to determine when pager on checkout/cart/index will be visible
     */
    const XPATH_CONFIG_NUMBER_ITEMS_TO_DISPLAY_PAGER = 'checkout/cart/number_items_to_display_pager';

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\Collection
     */
    private $itemsCollection;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory
     *
     */
    private $itemCollectionFactory;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    private $joinAttributeProcessor;

    /**
     * @var bool
     */
    private $isPagerDisplayed;

    /**
     * @var \Magento\Checkout\Model\CompositeConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrlBuilder,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Amasty\RequestQuote\Model\Quote\Session $amastyQuoteSession,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $joinProcessor,
        \Amasty\RequestQuote\Model\CompositeConfigProvider $configProvider,
        array $data = []
    ) {
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->joinAttributeProcessor = $joinProcessor;
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $catalogUrlBuilder,
            $cartHelper,
            $httpContext,
            $amastyQuoteSession,
            $data
        );
        $this->configProvider = $configProvider;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        if (!$this->isPagerDisplayedOnPage()) {
            parent::_construct();
        }
        if ($this->hasData('template')) {
            $this->setTemplate($this->getData('template'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->isPagerDisplayedOnPage()) {
            $availableLimit = (int)$this->_scopeConfig->getValue(
                self::XPATH_CONFIG_NUMBER_ITEMS_TO_DISPLAY_PAGER,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $itemsCollection = $this->getItemsForGrid();
            /** @var  \Magento\Theme\Block\Html\Pager $pager */
            $pager = $this->getLayout()->createBlock(\Magento\Theme\Block\Html\Pager::class);
            $pager->setAvailableLimit([$availableLimit => $availableLimit])->setCollection($itemsCollection);
            $this->setChild('pager', $pager);
            $itemsCollection->load();
            $this->prepareItemUrls();
        }
        return $this;
    }

    /**
     * @return \Magento\Quote\Model\ResourceModel\Quote\Item\Collection
     */
    public function getItemsForGrid()
    {
        if (!$this->itemsCollection) {
            /** @var \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $itemCollection */
            $itemCollection = $this->itemCollectionFactory->create();

            $itemCollection->setQuote($this->getQuote());
            $itemCollection->addFieldToFilter('parent_item_id', ['null' => true]);
            $this->joinAttributeProcessor->process($itemCollection);

            $this->itemsCollection = $itemCollection;
        }
        return $this->itemsCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        if (!$this->isPagerDisplayedOnPage()) {
            return parent::getItems();
        }
        return $this->getItemsForGrid()->getItems();
    }

    /**
     * @return bool
     */
    private function isPagerDisplayedOnPage()
    {
        if (!$this->isPagerDisplayed) {
            $availableLimit = (int)$this->_scopeConfig->getValue(
                self::XPATH_CONFIG_NUMBER_ITEMS_TO_DISPLAY_PAGER,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $this->isPagerDisplayed = !$this->getCustomItems() && $availableLimit < $this->getItemsCount();
        }
        return $this->isPagerDisplayed;
    }

    /**
     * @return array
     */
    public function getCheckoutConfig()
    {
        $config = $this->configProvider->getConfig();
        if (isset($config['quoteData']['remarks'])) {
            $remarks = json_decode($config['quoteData']['remarks'], true);
            if (isset($remarks['customer_note'])) {
                $config['quoteData']['remarks'] = $remarks['customer_note'];
            }
        }
        $config['checkoutUrl'] = $this->getUrl('amasty_quote/cart/submit');

        return $config;
    }

    /**
     * @return string
     */
    public function getSerializedCheckoutConfig()
    {
        /**
         * @TODO use own serializer
         */
        return json_encode($this->getCheckoutConfig(), JSON_HEX_TAG);
    }
}
