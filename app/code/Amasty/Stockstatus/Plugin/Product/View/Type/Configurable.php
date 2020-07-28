<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Plugin\Product\View\Type;

use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Amasty\Stockstatus\Model\Source\Outofstock;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as NativeConfigurable;

class Configurable
{
    /**
     * @var \Magento\Catalog\Helper\Product
     */
    private $catalogProduct;
    
    /**
     * @var \Magento\CatalogInventory\Model\StockRegistry
     */
    private $stockRegistry;
    
    /**
     * @var \Amasty\Stockstatus\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\ConfigurableProduct\Helper\Data
     */
    private $configurableHelper;

    /**
     * @var array
     */
    private $originalAllowedProducts = [];

    /**
     * @var DecoderInterface
     */
    private $jsonDecoder;

    public function __construct(
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Amasty\Stockstatus\Helper\Data $helper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\CatalogInventory\Model\StockRegistry $stockRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\ConfigurableProduct\Helper\Data $configurableHelper
    ) {
        $this->catalogProduct = $catalogProduct;
        $this->stockRegistry = $stockRegistry;
        $this->helper = $helper;
        $this->jsonEncoder = $jsonEncoder;
        $this->storeManager = $storeManager;
        $this->configurableHelper = $configurableHelper;
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * @param $subject
     * @return array
     */
    public function beforeGetAllowProducts(
        $subject
    ) {
        if (!$subject->hasAllowProducts() &&
            $this->helper->getOutofstockVisibility() != Outofstock::MAGENTO_LOGIC
        ) {
            $products = [];
            $websiteId =  $this->storeManager->getWebsite()->getId();
            $allProducts = $subject->getProduct()->getTypeInstance()->getUsedProducts($subject->getProduct(), null);
            foreach ($allProducts as $product) {
                /* remove code for showing out of stock options*/
                if ($product->getStatus() == Status::STATUS_ENABLED) {
                    $products[] = $product;
                }
                $stockStatus = $this->stockRegistry->getStockStatus(
                    $product->getId(),
                    $websiteId
                );
                if ($stockStatus->getStockStatus()) {
                    $this->originalAllowedProducts[] = $product;
                }
            }
            $subject->setAllowProducts($products);
        }

        return [];
    }

    public function afterToHtml(
        $subject,
        $html
    ) {
        if (in_array(
            $subject->getNameInLayout(),
            ['product.info.options.configurable', 'product.info.options.swatches']
        )
            && strpos($html, 'amstockstatusRenderer.init') === false
        ) {
            $instance = $subject->getProduct()->getTypeInstance(true);
            $allProducts = $instance->getUsedProducts($subject->getProduct());
            $_attributes = $instance->getConfigurableAttributes($subject->getProduct());

            $aStockStatus = [];
            foreach ($allProducts as $product) {
                $key = [];
                foreach ($_attributes as $attribute) {
                    $key[] = $product->getData(
                        $attribute->getData('product_attribute')->getData(
                            'attribute_code'
                        )
                    );
                }

                if ($key) {
                    $stockStatus = $this->stockRegistry->getStockStatusBySku(
                        $product->getSku(),
                        $this->storeManager->getWebsite()->getId()
                    );
                    $saleable = $stockStatus->getStockStatus() && $this->verifyStock($stockStatus);
                    $key =  implode(',', $key);

                    $aStockStatus[$key] = [
                       'is_in_stock'            => (int)$saleable,
                       'custom_status_text'     => $this->helper->getCustomStockStatusText($product),
                       'custom_status'          => $this->helper->showStockStatus($product),
                       'custom_status_icon'     => $this->helper->getStatusIconImage($product),
                       'custom_status_icon_only'=>
                           (int)$this->helper->getModuleConfig('general/icon_only'),
                       'product_id'             => $product->getId()
                    ];

                    if (!$saleable) {
                        $product->setData('is_salable', 0);
                        $aStockStatus[$key]['stockalert'] =
                            $this->helper->getStockAlert($product);
                    }
                    $aStockStatus[$key]['pricealert'] =
                        $this->helper->getPriceAlert($product);

                    if (!$aStockStatus[$key]['is_in_stock'] && !$aStockStatus[$key]['custom_status']) {
                        $aStockStatus[$key]['custom_status'] = __('Out of Stock');
                        $aStockStatus[$key]['custom_status_text'] = __('Out of Stock');
                    }

                    /* add status for previous option when all statuses are the same*/
                    $pos = strrpos($key, ",");
                    if ($pos) {
                        $newKey = substr($key, 0, $pos);
                        if (array_key_exists($newKey, $aStockStatus)) {
                            if ($aStockStatus[$newKey]['custom_status'] !=  $aStockStatus[$key]['custom_status']) {
                                $aStockStatus[$newKey] = null;
                            }
                        } else {
                            $aStockStatus[$newKey] =  $aStockStatus[$key];
                        }
                    }
                }
            }

            $aStockStatus['changeConfigurableStatus'] =
                (int)$this->helper->getModuleConfig("configurable_products/change_custom_configurable_status");
            $aStockStatus['type'] = $subject->getNameInLayout();
            $aStockStatus['info_block'] = $this->helper->getInfoBlock();

            $data = $this->jsonEncoder->encode($aStockStatus);

            $html  .=
                '<script>
                    require(["jquery", "jquery/ui", "Amasty_Stockstatus/js/amstockstatus"],
                    function ($, ui, amstockstatusRenderer) {
                        amstockstatusRenderer.init(' . $data . ');
                    });
                </script>';

        }

        return $html;
    }

    /**
     * @param \Magento\CatalogInventory\Api\Data\StockStatusInterface $stockStatus
     * @return bool
     */
    public function verifyStock($stockStatus)
    {
        $result = true;

        $stockItem = $stockStatus->getStockItem();
        if ($stockStatus->getQty() === null && $stockItem->getManageStock()) {
            $result = false;
        }

        if ($stockItem->getBackorders() == StockItemInterface::BACKORDERS_NO
            && $stockStatus->getQty() <= $stockStatus->getMinQty()
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param NativeConfigurable$subject
     * @param string $result
     *
     * @return string
     */
    public function afterGetJsonConfig($subject, $result)
    {
        $result = $this->jsonDecoder->decode($result);

        if ($this->helper->getOutofstockVisibility() === Outofstock::SHOW_AND_CROSSED) {
            $result['original_products'] = $this->configurableHelper->getOptions(
                $subject->getProduct(),
                $this->originalAllowedProducts
            );
        }

        return $this->jsonEncoder->encode($result);
    }
}
