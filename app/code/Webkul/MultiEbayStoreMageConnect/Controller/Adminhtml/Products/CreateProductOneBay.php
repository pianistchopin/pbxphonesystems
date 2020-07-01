<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Products;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\MultiEbayStoreMageConnect\Model\ProductmapFactory;
use Webkul\MultiEbayStoreMageConnect\Api\ProductmapRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Api\EbaycategorymapRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Api\CategoriesspecificationRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Products;

class CreateProductOneBay extends Products
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    private $stockStateInterface;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\Productmap
     */
    private $productMapRecord;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Helper\Data
     */
    private $helperData;

    /**
     * @var ProductmapRepositoryInterface
     */
    private $productMapRepository;

    /**
     * @var CategoriesspecificationRepositoryInterface
     */
    private $catSpecRepo;

    /**
     * @param Context                                           $context
     * @param JsonFactory                                       $resultJsonFactory
     * @param \Magento\Catalog\Model\Product                    $product
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface
     * @param Productmap                                        $productMapRecord
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\Data     $helperData
     * @param ProductmapRepositoryInterface                     $productMapRepository
     * @param EbaycategorymapRepositoryInterface                $ebayCategoryMapRepository
     * @param CategoriesspecificationRepositoryInterface        $catSpecRepo
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
        ProductmapFactory $productMapRecord,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helperData,
        ProductmapRepositoryInterface $productMapRepository,
        EbaycategorymapRepositoryInterface $ebayCategoryMapRepository,
        CategoriesspecificationRepositoryInterface $catSpecRepo,
        \Webkul\MultiEbayStoreMageConnect\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->product = $product;
        $this->stockStateInterface = $stockStateInterface;
        $this->productMapFactory = $productMapRecord;
        $this->helperData = $helperData;
        $this->productMapRepository = $productMapRepository;
        $this->ebayCategoryMapRepository = $ebayCategoryMapRepository;
        $this->catSpecRepo = $catSpecRepo;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $ruleId  = $data['ruleId'];
        $resultJson = $this->_resultJsonFactory->create();
        $validatedData = $this->validateData($data);
        if (!$validatedData['error']) {
            $client = $this->helperData->getEbayAPI($ruleId);
            $eBayDefaultSetting = $this->helperData->getEbayDefaultSettings($ruleId);
            $product = $validatedData['product'];
            $mainProSku = $product->getSku();
            $proTypeId = $product->getTypeId();
            $mageCategory = $product->getCategoryIds();
            $dataForEbayProduct = $this->getProductDetailForEbay($validatedData['ebay_cat_map_list'], $product);
            $primaryCategory = $dataForEbayProduct['primary_category'];
            $nameValueList = $dataForEbayProduct['name_value_list'];
            $conditionId = $dataForEbayProduct['condition_id'];

            $pictureDetails = $this->helperData->getPictureUrl($product);
            $quantity = $this->stockStateInterface->getStockQty($data['product']);
            $productDescription = $this->helperData->getProductDescription($product, $eBayDefaultSetting, $this->helperData->config['template_id']);
            $weight = $product->getWeight();

            /** apply price rules */
            $productCost = $product->getPrice();
            $productCost = $this->helperData->getPriceAfterAppliedRule($productCost, 'export');

            $item = [
                'ListingType' => 'FixedPriceItem',
                'Currency' => $eBayDefaultSetting['Currency'],
                'SKU' => $product->getSku(),
                'PaymentMethods' => 'PayPal',
                'PayPalEmailAddress' => $eBayDefaultSetting['PayPalEmailAddress'],
                'ConditionID' => $conditionId,
                'Country' => $eBayDefaultSetting['Country'],
                'ListingDuration' => $eBayDefaultSetting['ListingDuration'],
                'Title' => $product->getName(),
                'SubTitle' => substr($product->getName(), 0, 55),
                'PictureDetails' => $pictureDetails,
                'Description' => $productDescription,
                'Quantity' => $quantity ? $quantity : $eBayDefaultSetting['DefaultProQty'],
                'ItemSpecifics' => $nameValueList,
                'PostalCode' => $eBayDefaultSetting['PostalCode'],
                'DispatchTimeMax' => $eBayDefaultSetting['DispatchTimeMax'],
                'ReturnPolicy' => $eBayDefaultSetting['ReturnPolicy'],
                'ShippingDetails' => $eBayDefaultSetting['ShippingDetails'],
                'ShippingPackageDetails' => [
                    'MeasurementUnit' => 'English',
                    'WeightMajor' => $weight,
                    'WeightMinor' => (int)$weight-0.5
                ],
                'StartPrice' => $productCost,
                'PrimaryCategory' => $primaryCategory,
                'CategoryMappingAllowed' => true,
                'ProductListingDetails' => $dataForEbayProduct['pro_listing_details']
            ];
            if (!empty($dataForEbayProduct['ebay_store_category'])) {
                $item['Storefront'] = $dataForEbayProduct['ebay_store_category'];
            }
            $item = $this->getFormatedItemDetailsAsProductType($product, $nameValueList, $eBayDefaultSetting, $item);

            /* Get category details from ebay **/
            $eBayConfig = $this->helperData->geteBayConfiguration();

            $params = ['Version' => 891, 'Item' => $item];

            $results = $client->AddFixedPriceItem($params);
            if (isset($results->Ack) && ((string) $results->Ack == 'Success' || (string) $results->Ack == 'Warning')) {
                $data = [
                    'magento_pro_id' => $data['product'],
                    'mage_cat_id' => $mageCategory[0],
                    'sku' => $item['SKU'], /** $product->getSku(),*/
                    'name' => $item['Title'], /** $product->getName() */
                    'price' => $productCost,
                    'product_type' => $proTypeId,
                    'ebay_pro_id' => $results->ItemID,
                    'rule_id'   => $ruleId
                ];
                $record = $this->productMapFactory->create();
                $record->setData($data)->save();
                $result = ['ebay_pro' => $results->ItemID];
            } else {
                $messageClass = ['Warning' => 'notice', 'Error' => 'error'];
                if (isset($results->Message)) {
                    $errors[] = ['type' => 'notice', 'message' => $results->Message];
                }
                if (isset($results->Errors) && is_object($results->Errors)) {
                    $errors[] = [
                        'type' => $messageClass[$results->Errors->SeverityCode],
                        'message' => $results->Errors->LongMessage
                                        .' (eBay error code - '.$results->Errors->ErrorCode.')'
                    ];
                } else {
                    if (isset($results->Errors)) {
                        foreach ($results->Errors as $error) {
                            $errors[] = [
                                'type' => $messageClass[$error->SeverityCode],
                                'message' => $error->LongMessage .' (eBay error code - '.$error->ErrorCode.')'
                            ];
                        }
                    } else {
                        $errors[] = isset($results->detail->FaultDetail->SeverityCode) ?
                            [
                                'type' => $messageClass[$results->detail->FaultDetail->SeverityCode],
                                'message' => $results->detail->FaultDetail->LongMessage
                                                .' (eBay error code - '.$results->detail->FaultDetail->ErrorCode.')'
                            ] :
                            [
                                'type' => $messageClass[$results->detail->FaultDetail->Severity],
                                'message' => $results->detail->FaultDetail->DetailedMessage
                                                .' (eBay error code - '.$results->detail->FaultDetail->ErrorCode.')'
                            ];
                    }
                }
                $result = ['error' => 1,'product_sku' => $mainProSku, 'error_list' => $errors];
            }
        } else {
            $errors[] = ['type' => 'error', 'message' => $validatedData['msg']];
            $result = ['error' => 1,'product_sku' => '', 'error_list' => $errors];
        }
        return $resultJson->setData($result);
    }

    /**
     * get ProductListingDetails
     * @param Webkul\Ebaymagentoconnect\Model\Ebaycategorymap $catMapedRecord
     * @return array
     */
    private function getProductListingDetails($eBayCatMap, $product)
    {
        $listingDetail = [];
        if ($eBayCatMap->getEanStatus() != 0 || $eBayCatMap->getEanStatus() !='disabled') {
            $listingDetail['EAN'] = $product->getEan() ? $product->getEan() : 'Non applicabile';
        } else {
            $listingDetail['EAN'] = 'Non applicabile';
        }

        if ($eBayCatMap->getUpcStatus() != 0 || $eBayCatMap->getUpcStatus() !='disabled') {
            $listingDetail['UPC'] = $product->getUpc() ? $product->getUpc() : 'Non applicabile';
        } else {
            $listingDetail['UPC'] = 'Non applicabile';
        }
        return $listingDetail;
    }

    /**
     * getProductDetailForEbay
     * @param Webkul\MpEbaymagentoconnect\Model\ResourceModel\Ebaycategorymap\CollectionFactory $eBayCatMapList
     * @param Magento\Catalog\Model\Product $product
     * @return array
     */
    private function getProductDetailForEbay($eBayCatMapList, $product)
    {
        try {
            $nameValueList = [];
            $conditionId = 1000;
            foreach ($eBayCatMapList as $eBayCatMap) {
                $primaryCategory = ['CategoryID' => $eBayCatMap->getEbayCatId()];

                // store category
                $eBayStoreCatData = $eBayCatMap->getEbayStoreCatId();
                $eBayStoreCatArr = [];
                if ($eBayStoreCatData) {
                    $num = '';
                    $eBayStoreCatData = $this->jsonHelper->jsonDecode($eBayStoreCatData);
                    foreach ($eBayStoreCatData as $eBayStoreCatId) {
                        $eBayStoreCatArr['StoreCategory'.$num.'ID'] = $eBayStoreCatId;
                        $num = 2;
                    }
                }

                $proListingDetails = $this->getProductListingDetails($eBayCatMap, $product);

                if ($eBayCatMap->getProConditionAttr() != 'N/A') {
                    $conditionId = $product->getAttributeText($eBayCatMap->getProConditionAttr());
                    if ($conditionId != '') {
                        $conditionId = explode(' for ', $conditionId);
                        $conditionId = (int) $conditionId[0];
                    } else {
                        $conditionId = 1000; /** for new product */
                    }
                }


                $eBayCatSpecificsList = $this->catSpecRepo
                                        ->getCollectionByeBayCatId(
                                            $eBayCatMap->getEbayCatId()
                                        );

                foreach ($eBayCatSpecificsList as $eBayCatSpecifics) {
                    $value = $product->getAttributeText($eBayCatSpecifics->getMageProductAttributeCode());
                    if ($value == '') {
                        $tempData = $product->getData();
                        $attrCode = $eBayCatSpecifics->getMageProductAttributeCode();
                        $value = isset($tempData[$attrCode]) ? $tempData[$attrCode] : '';
                    }
                    $nameValueList[] = ['Name' => $eBayCatSpecifics->getEbaySpecificationName(), 'Value' => $value];
                }
            }
            $response = [
                 'condition_id' => $conditionId,
                 'name_value_list' => $nameValueList,
                 'primary_category' => $primaryCategory,
                 'pro_listing_details' => $proListingDetails,
                 'ebay_store_category' => $eBayStoreCatArr
            ];
            return $response;
        } catch (\Exception $e) {
            $this->logger->addError($e->getMessage());
        }
    }

   /**
    * getFormatedItemDetailsAsProductType
    *
    */
    private function getFormatedItemDetailsAsProductType($product, $nameValueList, $eBayDefaultSetting, $item)
    {
        try {
            $variations = '';
            $proTypeId = $product->getTypeId();
            $defaultProQty = $eBayDefaultSetting['DefaultProQty'];
            if ($proTypeId == 'configurable') {
                $variations = $this->helperData->getProductVariationForEbay($product, $nameValueList);
                if (!$item['ShippingPackageDetails']['WeightMajor']) {
                    $product = $this->product->load($variations['AssoProId']);
                    $weight = $product->getWeight();
                    $item['ShippingPackageDetails']['WeightMajor'] = $weight;
                    $item['ShippingPackageDetails']['WeightMinor'] = (int)$weight-0.5;
                }
                unset($variations['AssoProId']);
            } elseif ($proTypeId == 'bundle') {
                $variations = $this->helperData->getBundleProductVariationForEbay($product, $defaultProQty);
            } elseif ($proTypeId == 'grouped') {
                $variations = $this->helperData->getGroupedProductVariationForEbay($product, $defaultProQty);
            } elseif ($product->getHasOptions()) {
                $variations = $this->helperData->getOptionsIneBayVariationsFormat($product, $defaultProQty);
            }
            if ($variations != '') {
                $item['Variations'] = $variations;
                unset($item['StartPrice']);
                unset($item['Quantity']);
            }
            return $item;
        } catch (\Exception $e) {
            $this->logger->addError('getFormatedItemDetailsAsProductType : '.$e->getMessage());
            return $item;
        }
    }

    /**
     * validateData
     * @param array $data
     * @return array $result
     */
    private function validateData($data)
    {
        if (isset($data['product']) && isset($data['ruleId'])) {
            $product = $this->product->load($data['product']);
            if ($product->getEntityId()) {
                $mapRecord = $this->productMapRepository
                                ->getRecordByMageProductId($product->getEntityId())
                                ->setPageSize(1)->getFirstItem();
                $mageCategory = $product->getCategoryIds();
                $mageCategory = empty($mageCategory) ? [0] : $mageCategory;

                $eBayCatMapList = $this->ebayCategoryMapRepository
                                    ->getCollectionByMageCateIdsnRuleId($mageCategory, $data['ruleId']);
                if (!$mapRecord->getEntityId()) {
                    $result = $eBayCatMapList->getSize() > 0 ?
                        ['error' => 0, 'product' => $product, 'ebay_cat_map_list' => $eBayCatMapList] :
                        ['error' => 1, 'msg' => $product->getName().__(' category did not map to ebay category')];
                } else {
                    $result = ['error' => 1, 'msg' => $product->getName(). __(' already maped with eBay item')];
                }
            } else {
                $result = ['error' => 1,'msg' => __('Invalid Product')];
            }
        } else {
            $result = ['error' => 1,'msg' => __('Invalid request')];
        }
        return $result;
    }
}
