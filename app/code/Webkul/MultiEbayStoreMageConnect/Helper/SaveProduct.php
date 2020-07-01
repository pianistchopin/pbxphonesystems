<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Helper;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper as InitializationHelper;
use Magento\Catalog\Controller\Adminhtml\Product\Builder as ProductBuilder;
use Magento\ConfigurableProduct\Model\Product\VariationHandler;

class SaveProduct
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $_product;

    /**
     * @var Initialization\Helper
     */
    private $_initializationHelper;

    /**
     * @var \Magento\Catalog\Model\Product\TypeTransitionManager
     */
    private $_productTypeManager;

    /** @var \Magento\ConfigurableProduct\Model\Product\VariationHandler */
    private $_variationHandler;

    /** @var \Magento\Catalog\Api\ProductRepositoryInterface  */
    private $_productRepository;

    /**
     * @param \Magento\Framework\Event\Manager                             $eventManager
     * @param \Magento\Catalog\Model\Product                               $product
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager         $productTypeManager
     * @param \Magento\ConfigurableProduct\Model\Product\VariationHandler  $variationHandler
     * @param \Magento\Catalog\Api\ProductRepositoryInterface              $productRepository
     * @param Initialization\Helper                                        $initializationHelper
     * @param Builder                                                      $productBuilder
     */
    public function __construct(
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager,
        VariationHandler $variationHandler,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        InitializationHelper $initializationHelper,
        ProductBuilder $productBuilder
    ) {
        $this->_product = $product;
        $this->_initializationHelper = $initializationHelper;
        $this->_productBuilder = $productBuilder;
        $this->_productTypeManager = $productTypeManager;
        $this->_variationHandler = $variationHandler;
        $this->_productRepository = $productRepository;
    }

    /**
     * Default customer account page.
     * @return \Magento\Framework\View\Result\Page
     */
    public function saveProductData($proDataReq, $storeId = 0)
    {
        $wholedata = $proDataReq->getParams();
        $product = $this->_initializationHelper
                                ->initialize(
                                    $this->_productBuilder->build(
                                        $proDataReq,
                                        $storeId
                                    )
                                );

        $this->_productTypeManager->processProduct($product);
        $product->setUrlKey($product->getName().rand(1, 100));
        $originalSku = $product->getSku();
        try {
            $product->save();
        } catch (\Excetpion $e) {
            return 0;
        }
        $productId = $product->getId();

        $configurations = [];
        if (!empty($wholedata['supperattr'])) {
            $configurations = $wholedata['supperattr'];
        }
        /** for configurable associated product */
        if ($product->getTypeId() == ConfigurableProduct::TYPE_CODE
            && !empty($configurations)) {
            $configurations = $this->_variationHandler
                                    ->duplicateImagesForVariations(
                                        $configurations
                                    );
            foreach ($configurations as $associtedProductId => $productData) {
                $associtedProduct = $this->_productRepository->getById(
                    $associtedProductId,
                    true,
                    $storeId
                );
                $productData = $this->_variationHandler->processMediaGallery(
                    $associtedProduct,
                    $productData
                );
                $associtedProduct->addData($productData);
                if ($associtedProduct->hasDataChanges()) {
                    $this->_saveAssocitedProduct($associtedProduct);
                }
            }
        }

        /*for configurable associated products save end*/
        $this->_product->load($productId)
                        ->setStatus($wholedata['product']['status'])->save();
        return $productId;
    }

    /**
     * @param Magento\Catalog\Api\Data\ProductInterface $associtedProduct
     * @return void
     */
    private function _saveAssocitedProduct($associtedProduct)
    {
        $this->_productRepository->save($associtedProduct, true);
    }
}
