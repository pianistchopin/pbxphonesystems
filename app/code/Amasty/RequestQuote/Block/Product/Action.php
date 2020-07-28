<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Product;

class Action extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'Amasty_RequestQuote::product/addtoquote.phtml';
    /**
     * @var \Amasty\RequestQuote\Helper\Cart
     */
    private $cartHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Amasty\RequestQuote\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    private $category;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var array
     */
    private $allowedProductTypes = [
        'simple',
        'configurable',
        'downloadable',
        'virtual',
        'grouped',
        'bundle'
    ];

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request,
        \Amasty\RequestQuote\Helper\Data $helper,
        \Amasty\RequestQuote\Helper\Cart $cartHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->request = $request;
        $this->helper = $helper;
        $this->cartHelper = $cartHelper;
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @return string
     */
    public function getAddUrl()
    {
        if (!$this->isCategoryPage()) {
            return $this->cartHelper->getAddUrl($this->getProduct());
        }
        return '';
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $html = '';
        $canDisplayButton = $this->isCategoryPage()
            ? $this->helper->displayByuButtonOnListing() : $this->helper->displayByuButtonOnPdp();
        if ($canDisplayButton
            && $this->helper->isActive()
            && !$this->getProduct()->getData(\Amasty\RequestQuote\Helper\Data::ATTRIBUTE_NAME_HIDE_BUY_BUTTON)
            && empty(array_uintersect(
                $this->getProduct()->getCategoryIds(),
                $this->helper->getExcludeCategories(),
                'strcmp'
            ))
            && in_array($this->getProduct()->getTypeId(), $this->allowedProductTypes)
        ) {
            if ($this->helper->isAllowedCustomerGroup()) {
                $this->setButtonText(__('Add to Quote'));
                $this->setLoggedIn(true);
                $html = parent::toHtml();
            } elseif (!$this->helper->isLoggedIn() && $this->helper->isInformGuests()) {
                $this->setButtonText($this->getGuestButtonText());
                $this->setLoggedIn(false);
                $html = parent::toHtml();
            }
        }

        return $html;
    }

    /**
     * @return bool
     */
    public function isGuest()
    {
        return !$this->helper->isLoggedIn();
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if ($product = $this->registry->registry('current_product')) {
            $this->product = $product;
        } else {
            $this->product = $this->getParentBlock()->getProduct();
        }

        if (!$this->product instanceof \Magento\Catalog\Model\Product) {
            $this->product = $this->productFactory->create();
        }

        return $this->product;
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        if ($product = $this->registry->registry('current_category')) {
            $this->category = $product;
        }

        if (!$this->category instanceof \Magento\Catalog\Model\Product) {
            $this->category = $this->productFactory->create();
        }

        return $this->category;
    }

    /**
     * @return bool
     */
    public function isCategoryPage()
    {
        return $this->registry->registry('current_category') !== null
            && !$this->registry->registry('current_product');
    }

    /**
     * @return string
     */
    public function getGuestButtonText()
    {
        return $this->escapeHtml($this->helper->getGuestButtonText());
    }
}
