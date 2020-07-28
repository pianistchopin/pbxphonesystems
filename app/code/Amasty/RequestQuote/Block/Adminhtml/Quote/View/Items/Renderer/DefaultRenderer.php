<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\View\Items\Renderer;

use Magento\Quote\Model\Quote\Item;

class DefaultRenderer extends \Amasty\RequestQuote\Block\Adminhtml\Items\Renderer\DefaultRenderer
{
    /**
     * @var \Magento\Checkout\Helper\Data
     */
    private $checkoutHelper;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var \Magento\Tax\Model\Config
     */
    private $taxConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $quoteSession,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = []
    ) {
        $this->checkoutHelper = $checkoutHelper;
        $this->serializer = $serializer;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $quoteSession, $currencyFactory, $data);
        $this->taxConfig = $taxConfig;
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->_getData('item');
    }

    /**
     * @param string $id
     * @return string
     */
    public function getFieldId($id)
    {
        return $this->getFieldIdPrefix() . $id;
    }

    /**
     * @return string
     */
    public function getFieldIdPrefix()
    {
        return 'quote_item_' . $this->getItem()->getId() . '_';
    }

    /**
     * @return bool
     */
    public function canDisplayContainer()
    {
        return $this->getRequest()->getParam('reload') != 1;
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return substr($this->getFieldIdPrefix(), 0, -1);
    }

    /**
     * @param Item $item
     * @return string
     */
    public function displaySubtotalInclTax($item)
    {
        return $this->displayPrices(
            $this->checkoutHelper->getBaseSubtotalInclTax($item),
            $this->checkoutHelper->getSubtotalInclTax($item)
        );
    }

    /**
     * @param Item|\Magento\Framework\DataObject $item
     * @return string
     */
    public function displayPriceInclTax(\Magento\Framework\DataObject $item)
    {
        return $this->displayPrices(
            $this->checkoutHelper->getBasePriceInclTax($item),
            $this->checkoutHelper->getPriceInclTax($item)
        );
    }

    /**
     * @param \Magento\Framework\DataObject|Item $item
     * @param string $column
     * @param null $field
     * @return string
     */
    public function getColumnHtml(\Magento\Framework\DataObject $item, $column, $field = null)
    {
        $html = '';
        switch ($column) {
            case 'product':
                if ($this->canDisplayContainer()) {
                    $html .= '<div id="' . $this->getHtmlId() . '">';
                }
                $html .= $this->getColumnHtml($item, 'name');
                if ($this->canDisplayContainer()) {
                    $html .= '</div>';
                }
                break;
            case 'price-original':
                $html = $this->displayPriceAttribute('price');
                break;
            case 'product-price':
                $html = $this->displayProductPrice();
                break;
            case 'price':
                $html = $item->getCustomPrice() ? $this->displayCustomPrice() : $this->displayProductPrice();
                break;
            case 'qty':
                $html = $item->getQty() * 1;
                break;
            case 'subtotal':
                $html = $this->displayPriceAttribute('subtotal');
                break;
            case 'total':
                $code = $this->taxConfig->priceIncludesTax() ? 'row_total_incl_tax' : 'row_total';
                $html = $this->displayPriceAttribute($code);
                break;

            default:
                $html = parent::getColumnHtml($item, $column, $field);
        }
        return $html;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        $columns = array_key_exists('columns', $this->_data) ? $this->_data['columns'] : [];
        return $columns;
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function getItemQuestion(\Magento\Quote\Model\Quote\Item $item)
    {
        try{
            /**
             * @TODO implement own serializer
             */

            set_error_handler(
                function ($errno, $errstr) {
                    restore_error_handler();
                    throw new \InvalidArgumentException('Unable to unserialize value, string is corrupted.');
                },
                E_NOTICE
            );
            $itemNotes = $this->serializer->unserialize($item->getAdditionalData());
            restore_error_handler();
            return isset($itemNotes['customer_note']) ? $itemNotes['customer_note'] : '';
        }catch (\InvalidArgumentException $e) {

        }
        return '';
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function getItemAnswer(\Magento\Quote\Model\Quote\Item $item)
    {
        $itemNotes = $this->serializer->unserialize($item->getAdditionalData());
        return isset($itemNotes['admin_note']) ? $itemNotes['admin_note'] : '';
    }
}
