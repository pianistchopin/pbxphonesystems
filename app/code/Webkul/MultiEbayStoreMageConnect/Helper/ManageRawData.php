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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Webkul\MultiEbayStoreMageConnect\Api\ImportedtmpproductRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Api\EbayaccountsRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Model\Importedtmpproduct;
use Webkul\MultiEbayStoreMageConnect\Api\ProductmapRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Api\OrdermapRepositoryInterface;

class ManageRawData
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\Ordermap
     */
    private $orderMapRecord;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Logger\Logger
     */
    private $logger;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\Productmap
     */
    private $productMapRecord;

    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Helper\Data
     */
    private $helper;

    /**
     * @var EbayaccountsRepositoryInterface
     */
    private $eBayAccountsRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    private $attributeModel;

    /**
     * @var Importedtmpproduct
     */
    private $importedTmpProduct;

    /**
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\Data             $helper
     * @param ScopeConfigInterface                                      $scopeConfig
     * @param \Webkul\MultiEbayStoreMageConnect\Model\Productmap        $productMapRecord
     * @param \Magento\Framework\Registry                               $registry
     * @param \Webkul\MultiEbayStoreMageConnect\Model\Ordermap          $orderMapRecord
     * @param \Webkul\MultiEbayStoreMageConnect\Logger\Logger           $logger
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\Data             $helper
     * @param ImportedtmpproductRepositoryInterface                     $importedTmpProductRepository
     * @param EbayaccountsRepositoryInterface                           $eBayAccountsRepository
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeModel
     * @param Importedtmpproduct                                        $importedTmpProduct
     * @param ProductmapRepositoryInterface                             $productMapRepository
     * @param OrdermapRepositoryInterface                               $orderMapRepository
     */
    public function __construct(
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
        ScopeConfigInterface $scopeConfig,
        \Webkul\MultiEbayStoreMageConnect\Model\Productmap $productMapRecord,
        \Magento\Framework\Registry $registry,
        \Webkul\MultiEbayStoreMageConnect\Model\Ordermap $orderMapRecord,
        \Webkul\MultiEbayStoreMageConnect\Logger\Logger $logger,
        ImportedtmpproductRepositoryInterface $importedTmpProductRepository,
        EbayaccountsRepositoryInterface $eBayAccountsRepository,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeModel,
        Importedtmpproduct $importedTmpProduct,
        ProductmapRepositoryInterface $productMapRepository,
        OrdermapRepositoryInterface $orderMapRepository,
        \Magento\Directory\Model\Region $region,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->contextController = $context;
        $this->_scopeConfig = $scopeConfig;
        $this->productMapRecord = $productMapRecord;
        $this->registry = $registry;
        $this->orderMapRecord = $orderMapRecord;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->_importedTmpProductRepository = $importedTmpProductRepository;
        $this->eBayAccountsRepository = $eBayAccountsRepository;
        $this->attributeModel = $attributeModel;
        $this->importedTmpProduct = $importedTmpProduct;
        $this->_productMapRepository = $productMapRepository;
        $this->_orderMapRepository = $orderMapRepository;
        $this->region = $region;
    }

    /**
     * manage raw data
     *
     * @param array $eBayOrders
     * @param int $ruleId
     * @param boolean $viaCron
     * @param boolean $viaListener
     * @return void
     */
    public function ManageOrderRawData($eBayOrders, $ruleId, $viaCron = false, $viaListener = false)
    {
        $items = [];
        $i = 0;
        $notifications = [];
        $errorMsg = '';
        $tempAvlImported = $this->_importedTmpProductRepository
                    ->getCollectionByProductTypeAndRuleId('order', $ruleId)
                    ->getColumnValues('item_id');
        ;
        foreach ($eBayOrders as $eBayOrder) {
            if (in_array($eBayOrder['OrderID'], $tempAvlImported)) {
                continue;
            }
            /****/
            $firstname = 'Guest';
            $lastname = 'User';
            $shipPrice = 0;
            $shipMethod = __('From eBay ');
            if (isset($eBayOrder['ShippingServiceSelected']['ShippingService'])) {
                $shipMethod .= $eBayOrder['ShippingServiceSelected']['ShippingService'];
            }

            $orderItemsData = $this->getOrderItemList(
                $eBayOrder['TransactionArray']['Transaction'],
                $eBayOrder['OrderID'],
                $ruleId
            );

            if (!$orderItemsData['invalid_order']) {
                foreach ($eBayOrder['ShippingAddress'] as $key => $value) {
                    $eBayOrder['ShippingAddress'][$key] = ($value == '') ?  __('NA') : $value;
                }
                if (!isset($eBayOrder['ShippingAddress']['Country'])) {
                    $eBayOrder['ShippingAddress']['Country'] = __('NA');
                }

                $region = $this->getOrderRegion($eBayOrder['ShippingAddress']);
                $tempOrder = [
                    'ebay_order_id' => $eBayOrder['OrderID'],
                    'order_status' => $eBayOrder['OrderStatus'],
                    'currency_id' => $eBayOrder['AdjustmentAmount']['currencyID'],
                    'email' => $eBayOrder['SellerEmail'],
                    'shipping_address' => [
                        'firstname' => $orderItemsData['firstname'],
                        'lastname' => $orderItemsData['lastname'],
                        'street' => $eBayOrder['ShippingAddress']['Street1']."\r\n"
                                                    .$eBayOrder['ShippingAddress']['Street2'],
                        'city' => $eBayOrder['ShippingAddress']['CityName'],
                        'country_id' => $eBayOrder['ShippingAddress']['Country'],
                        'country_name' => $eBayOrder['ShippingAddress']['CountryName'],
                        'region' => $region,
                        'postcode' => $eBayOrder['ShippingAddress']['PostalCode'],
                        'telephone' => $eBayOrder['ShippingAddress']['Phone'],
                        'fax' => $eBayOrder['ShippingAddress']['Phone'],
                        'vat_id' => '',
                        'save_in_address_book' => 1
                    ],
                    'items' => $orderItemsData['order_items'],
                    'shipping_service' => ['method' => $shipMethod,'cost' => $orderItemsData['ship_price']],
                ];

                if ($viaListener) {
                    return $tempOrder;
                } elseif (!$viaCron) {
                    $dt = new \DateTime();
                    $currentDate = $dt->format('Y-m-d\TH:i:s');

                    $tempdata = [
                            'item_type' => 'order',
                            'item_id' => $tempOrder['ebay_order_id'],
                            'product_data' => json_encode($tempOrder),
                            'created_at' => $currentDate,
                            'rule_id' => $ruleId
                        ];
                    $tempOrderData = $this->importedTmpProduct;
                    $tempOrderData->setData($tempdata);
                    $item = $tempOrderData->save();
                    array_push($items, $item->getEntityId());
                } else {
                    $this->logger->info(' order raw data ');
                    $this->logger->info(json_encode($tempOrder));

                    $mapedOrder = $this->_orderMapRepository
                            ->getRecordByEbayOrderId($tempOrder['ebay_order_id'])
                            ->setPageSize(1)
                            ->getFirstItem();
                    if (!$mapedOrder->getEntityId()) {
                        $orderData = $this->helper->createMageOrder($tempOrder);
                    } else {
                        $this->logger->info(' order id '.$tempOrder['ebay_order_id'] .' already created ');
                        continue;
                    }
                    $this->logger->info(' created order data '.json_encode($orderData));
                    $items[$i]['order'] = $orderData;

                    if (isset($orderData['order_id']) && $orderData['order_id']) {
                        $data = [
                                'ebay_order_id' => $tempOrder['ebay_order_id'],
                                'mage_order_id' => $orderData['order_id'],
                                'status' => $tempOrder['order_status'],
                                'rule_id'   => $ruleId
                                //'order'=>$tempProData['price'],
                              ];
                        $record = $this->orderMapRecord;
                        $record->setData($data)->save();
                    }
                    $i++;
                }
            } else {
                $errorMsg = $errorMsg.$orderItemsData['error_msg'];
            }
        }
        $notifications['errorMsg'] = $errorMsg;
        $notifications['items'] = $items;
        return $notifications;
    }

    /**
     * getOrderItemList
     * @param array $transactionList
     * @param varchar $orderId
     * @return array
     */
    private function getOrderItemList($transactionList, $orderId, $ruleId)
    {
        try {
            if (!isset($transactionList[0])) {
                $transactionList = [0 => $transactionList];
            }
            $firstname = 'Guest';
            $lastname = 'User';
            $shipPrice = 0;
            $flagSetName = true;
            $invalidOrder = false;
            $orderItems = [];
            $errorMsg = '';
            $productId = 0;

            foreach ($transactionList as $transaction) {
                if (isset($transaction['ActualShippingCost']['_'])) {
                    $shipPrice = $shipPrice + floatval($transaction['ActualShippingCost']['_']);
                }
                if (isset($transaction['Buyer']['UserFirstName']) && $flagSetName) {
                    $firstname = $transaction['Buyer']['UserFirstName'];
                    $lastname = $transaction['Buyer']['UserLastName'];
                    $flagSetName = false;
                }
                $syncProMap = $this->productMapRecord->getCollection()
                                    ->addFieldToFilter('ebay_pro_id', $transaction['Item']['ItemID'])
                                    ->setPageSize(1)->getFirstItem();

                if ($syncProMap->getEntityId()) {
                    $productData = $this->getMagentoProductIdsAccordingtoeBayItem($syncProMap, $transaction);
                    if ($productData) {
                        $tmporderItem = [
                            'product_id' => $productData['product_id'],
                            'qty' => $transaction['QuantityPurchased'],
                            'price' => $transaction['TransactionPrice']['_'],
                        ];
                        if ($productData['bundle_items']) {
                            $tmporderItem['bundle_items'] = $productData['bundle_items'];
                        }
                        $orderItems[] = $tmporderItem;
                    } else {
                        $errorMsg = $errorMsg.' eBay order id : <b>'.$orderId."</b> not sync because Product <b>'"
                                        .$transaction['Item']['Title'].' ('.$transaction['Item']['ItemID'].')'
                                        ."'</b> not Synced on Magento <br />";
                        $invalidOrder = true;
                    }
                } else {
                    $this->importEbayProductByEbayItemId($transaction['Item']['ItemID'], $ruleId);
                    $errorMsg = $errorMsg.'eBay order id : <b>'.$orderId."</b> not sync because Product <b>'"
                                            .$transaction['Item']['Title'].' ('.$transaction['Item']['ItemID'].')'
                                            ."'</b> not Synced on Magento <br />";
                    $invalidOrder = true;
                }
            }

            $responce = [
                'error_msg' => $errorMsg,
                'invalid_order' => $invalidOrder,
                'order_items' => $orderItems,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'ship_price' => $shipPrice
            ];
            return $responce;
        } catch (\Exception $e) {
            $this->logger->addError('getOrderItemList : '.$e->getMessage());
            return ['error_msg' => $errorMsg.'<br />'.$e->getMessage(), 'invalid_order' => true];
        }
    }

    /**
     * importEbayProductByEbayItemId
     */
    private function importEbayProductByEbayItemId($itemId, $ruleId)
    {
        try {
            $client = $this->helper->getEbayAPI($ruleId);
            if ($client) {
                $params = [
                    'Version' => 659,
                    'DetailLevel' => 'ReturnAll',
                    'ItemID' => $itemId,
                    'IncludeItemSpecifics' => true
                ];
                $resultsProduct = $client->GetItem($params);
                $resultsProArray[] = $resultsProduct->Item;
                $this->manageProductRawData($resultsProArray, $ruleId);
            }
        } catch (\Exception $e) {
            $this->logger->addError('importEbayProductByEbayItemId :- '. $e->getMessage());
        }
    }


    /**
     * getMagentoProductIdsAccordingtoeBayItem
     */
    private function getMagentoProductIdsAccordingtoeBayItem($syncProMap, $transaction)
    {
        try {
            $productId = $syncProMap->getMagentoProId();
            $productType = $syncProMap->getProductType();
            $bundalProductItems = false;
            switch ($productType) {
                case 'configurable':
                    $nameValueList = $transaction['Variation']['VariationSpecifics']['NameValueList'];
                    if (!isset($nameValueList[0])) {
                        $nameValueList = [0 => $nameValueList];
                    }
                    $productId = $this->helper->getConfAssoProductId($productId, $nameValueList);
                    break;
                case 'grouped':
                    $productId = $this->helper->getProductRepository()
                                        ->get($transaction['Variation']['SKU'])->getEntityId();
                    break;
                case 'bundle':
                    $sku = $transaction['Variation']['SKU'];
                    $nameValueList = $transaction['Variation']['VariationSpecifics']['NameValueList'];
                    $bundalProductItems = $this->getBundleAssoProductIds($productId, $nameValueList, $sku);
                    break;
            }
            return ['product_id' => $productId, 'bundle_items' => $bundalProductItems];
        } catch (\Exception $e) {
            $this->logger->addError('getMagentoProductIdsAccordingtoeBayItem : '.$e->getMessage());
            return false;
        }
    }

    /**
     * getBundleAssoProductId
     * @param int $productId
     * @param array $nameValueList
     * @param string $sku
     * @return array $bundalProductItems
     */
    public function getBundleAssoProductIds($productId, $nameValueList, $sku)
    {
        $skuList = explode('-b-', $sku);
        $name = [];
        $bundalProductItems = [];
        foreach ($skuList as $sku) {
            $assoPro = $this->helper->getProductRepository()->get($sku);
            foreach ($nameValueList as $value) {
                if ($assoPro->getName() == $value['Value']) {
                    $name[$value['Value']] = $sku;
                    break;
                }
            }
        }

        $product = $this->helper->getProductRepository()->getById($productId);
        $options = $this->helper->getBundleProductOptions($product);
        foreach ($nameValueList as $nameValue) {
            foreach ($options as $option) {
                if ($nameValue['Name'] == $option->getDefaultTitle()) {
                    $bundalProductItems[$option->getOptionId()] = $name[$nameValue['Value']];
                    break;
                }
            }
        }
        return $bundalProductItems;
    }

    /**
     * getOrderRegion
     * @param array $shippingAddress
     * @return string
     */
    private function getOrderRegion($shippingAddress)
    {
        $region = $shippingAddress['StateOrProvince'];
        $addState = [];
        $requiredStates = $this->helper->getRequiredStateList();
        $requiredStatesArray = explode(',', $requiredStates);
        if (in_array($shippingAddress['Country'], $requiredStatesArray)) {
            $countryId = $shippingAddress['Country'];
            $regionData = $this->region->loadByCode($region, $countryId);
            if ($regionData->getRegionId()) {
                $region = $regionData->getRegionId();
            } else {
                $regionData = $this->region->loadByName('other', $countryId);
                if ($regionData->getRegionId()) {
                    $region = $regionData->getRegionId();
                } else {
                    $addState['country_id'] = $countryId;
                    $addState['code'] = 'other';
                    $addState['default_name'] = 'other';
                    $region = $this->region->setData($addState)->save()->getRegionId();
                }
            }
        }
        return $region;
    }

    /**
     * manage Product Raw Data
     *
     * @param object $resultsItems
     * @param int $ruleId
     * @param object $request
     * @param boolean $viaCron
     * @param boolean $viaListener
     * @return array
     */
    public function ManageProductRawData($resultsItems, $ruleId, $request = null, $viaCron = false, $viaListener = false)
    {
        $items = [];
        $this->ruleId = $ruleId;
        $alreadyMapped = [];
        $configData = $this->helper->geteBayConfiguration($ruleId);
        $this->attributeSetId = $configData['attribute_set'];
        if ($viaListener) {
            $tempAvlImported = [];
        } else {
            $tempAvlImported = $this->importedTmpProduct->getCollection()
                            ->addFieldToFilter('item_type', 'product')
                            ->addFieldToFilter('rule_id', $ruleId)
                            ->getColumnValues('item_id');
            $alreadyMapped = $this->productMapRecord->getCollection()
                            ->addFieldToFilter('rule_id', $ruleId)
                            ->getColumnValues('ebay_pro_id');

            $tempAvlImported =  array_merge($tempAvlImported, $alreadyMapped);
        }


        $helper = $this->helper;
        foreach ($resultsItems as $data) {
            $data = json_decode((json_encode($data)), true);
            $this->logger->info('managerwdata '.$data['ItemID']);
            if (in_array($data['ItemID'], $tempAvlImported)) {
                continue;
            }

            if (isset($configData['item_with_html']) && $configData['item_with_html']) {
                $data['Description'] = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $data['Description']);
                $data['Description'] = preg_replace('#<link(.*?)>(.*?)</link>#is', '', $data['Description']);
                $data['Description'] = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $data['Description']);
                $data['Description'] = strip_tags($data['Description']);
            }

            $wholedata = $this->prepareWholeData($data);

            //set Specification in wholedata
            if ($helper->isProductWithSpeci()) {
                $eBayClient = $this->helper->getEbayAPI($ruleId);
                $specification = $this->getItemSpecification($data['ItemID'], $configData['attribute_set'], $eBayClient);

                if ($specification && is_array($specification)) {
                    $wholedata['specification'] = $specification;
                }
            }
            $wholedata = $this->setImageData($wholedata, $data['PictureDetails']);
            /* Save imported product in temp table***/
            if ($viaListener) {
                return $wholedata;
            } elseif (!$viaCron) {
                $dt = new \DateTime();
                $currentDate = $dt->format('Y-m-d\TH:i:s');
                $tempdata = [
                            'item_type' => 'product',
                            'item_id' => $data['ItemID'],
                            'product_data' => json_encode($wholedata),
                            'created_at' => $currentDate,
                            'rule_id'   => $ruleId
                        ];
                $temppro = $this->importedTmpProduct;
                $temppro->setData($tempdata);
                $item = $temppro->save();
                array_push($items, $item->getEntityId());
            } else {
                $productId = $this->cronActionOnProductData($wholedata);
                return $productId;
            }
        }
        return $items;
    }

    /**
     * cronActionOnProductData
     * @param array $tempProData
     * @return int
     */

    private function cronActionOnProductData($tempProData)
    {
        try {
            $productId = 0;
            $request = $this->contextController->getRequest();
            if (($tempProData['type_id'] == 'simple') || (isset($tempProData['supperattr'])
                && empty($tempProData['supperattr']))) {
                if (isset($tempProData['assocate_pro'][0])) {
                    $tempProData['price'] = $tempProData['assocate_pro'][0]['price'];
                    $tempProData['stock'] = $tempProData['assocate_pro'][0]['qty'];
                    $tempProData['type_id'] = 'simple';
                    foreach ($tempProData['assocate_pro'][0] as $key => $value) {
                        if (strpos($key, 'conf_') !== false) {
                            $tempProData[$key] = $value;
                        }
                    }
                    unset($tempProData['assocate_pro']);
                    unset($tempProData['supperattr']);
                }
                foreach ($tempProData as $key => $value) {
                    $request->setParam($key, $value);
                }

                $result = $this->helper->saveSimpleProduct($request);
            } else {
                foreach ($tempProData as $key => $value) {
                    $request->setParam($key, $value);
                }
                $result = $this->helper->saveConfigProduct($request);
            }
            $data = [
                'ebay_pro_id' => $tempProData['sku'],
                'name' => $tempProData['name'],
                'price' => $tempProData['price'],
                'product_type' => $tempProData['type_id'],
                'rule_id'   => $this->helper->ruleId
            ];

            if (isset($result['product_id']) && $result['product_id']) {
                $productId = $data['magento_pro_id'] = $result['product_id'];
                $data['mage_cat_id'] = $tempProData['category'][0];
                $record = $this->productMapRecord;
                $record->setData($data);
                $record->save();
            }
            $this->registry->unregister('product');
            $this->registry->unregister('current_product');
            $this->registry->unregister('current_store');
            return $productId;
        } catch (\Exception $e) {
            $this->logger->info('product raw data -'.$this->jsonHelper->jsonEncode($tempProData));
            $this->logger->addError('Error :- '. $e->getMessage());
            return $productId;
        }
    }

    /**
     * prepareWholeData
     * @param array $data
     * @return array
     */
    private function prepareWholeData($data)
    {
        try {
            $wholedata = [];
            $proCatSpec = [];
            $managedProData = $this->getManageProductData($data);
            $eBayMageId = $managedProData['ebay_mage_id'];
            $proCatSpec = $managedProData['pro_cat_spec'];
            $weight = $managedProData['weight'];
            $attributeSetId = $managedProData['attribute_set_id'];

            /* get product variation**/
            $this->logger->addError('prepareWholeData data:- '.json_encode($data['SellingStatus']['CurrentPrice']));
            $price = isset($data['SellingStatus']['CurrentPrice']['_']) ?
                        $data['SellingStatus']['CurrentPrice']['_'] : $data['SellingStatus']['CurrentPrice'];
            $productCost = $this->helper->getPriceAfterAppliedRule($price, 'import');
            $currencyId = isset($data['SellingStatus']['CurrentPrice']['currencyID']) ?
                $data['SellingStatus']['CurrentPrice']['currencyID'] : $data['Currency'];
            if (isset($data['Variations']) && !empty($data['Variations'])) {
                $itemSku = isset($data['SKU']) ? $data['SKU'] : $data['ItemID'];
                $superAttrAndAssociatePro = $this->getSuperAttrAndAssociatePro(
                    $data['Variations']['Variation'],
                    $itemSku,
                    $weight
                );

                $wholedata = [
                    'ebay_item_id' => $data['ItemID'],
                    'type_id' => 'configurable',
                    'supperattr' => $superAttrAndAssociatePro['super_att_ids'],
                    'status' => 1,
                    'attribute_set_id' => $attributeSetId,
                    'category' => [$eBayMageId],
                    'name' => $data['Title'],
                    'description' => $data['Description'],
                    'short_description' => ' ',
                    'sku' => isset($data['SKU']) ? $data['SKU'] : $data['ItemID'],
                    'price' => $productCost,
                    'currency_id' => $currencyId,
                    'is_in_stock' => 1,
                    'tax_class_id' => 0,
                    'weight' => $weight,
                    'listing_status' => $data['SellingStatus']['ListingStatus']
                ];

                /* Assigne values to store product according to eBay product**/
                foreach ($proCatSpec as $key => $value) {
                    $wholedata[$key] = $value;
                }

                $wholedata['assocate_pro'] = $superAttrAndAssociatePro['associate_pro'];
            } else {
                /**For without variation product**/
                $wholedata = [
                    'ebay_item_id' => $data['ItemID'],
                    'type_id' => 'simple',
                    'status' => 1,
                    'attribute_set_id' => $attributeSetId,
                    'producttypecustom' => 'customproductsimple',
                    'category' => [$eBayMageId],
                    'name' => $data['Title'],
                    'description' => $data['Description'],
                    'short_description' => ' ',
                    'sku' => isset($data['SKU']) ? $data['SKU'] : $data['ItemID'],
                    'price' => $productCost,
                    'currency_id' => $currencyId,
                    'stock' => $data['Quantity'],
                    'is_in_stock' => 1,
                    'tax_class_id' => 0,
                    'weight' => $weight,
                    'listing_status' => $data['SellingStatus']['ListingStatus']
                ];
                foreach ($proCatSpec as $key => $value) {
                    $wholedata[$key] = $value;
                }
            }
            return $wholedata;
        } catch (\Exception $e) {
            $this->logger->addError('prepareWholeData :- '. $e->getMessage());
            return $wholedata;
        }
    }

    /**
     * getSuperAttrAndAssociatePro
     *
     */
    private function getSuperAttrAndAssociatePro($variationsList, $itemId, $weight)
    {
        $superAttIds = [];
        $associatePro = [];
        if (isset($variationsList[0]) === false) {
            $variationsList = [0 => $variationsList];
        }
        $count = 1;
        foreach ($variationsList as $variation) {
            $attributeData = [];
            $variationSpecifics = $variation['VariationSpecifics'];
            if (isset($variationSpecifics['NameValueList'])) {
                if (isset($variationSpecifics['NameValueList']['Name'])) {
                    $variationSpecifics['NameValueList'] = [0 => $variationSpecifics['NameValueList']];
                }
                foreach ($variationSpecifics['NameValueList'] as $nameValue) {
                    $attributeCode = str_replace(' ', '_', $nameValue['Name']);
                    $attributeCode = preg_replace('/[^A-Za-z0-9\_]/', '', $attributeCode);
                    $mageAttrCode = substr('conf_'.strtolower($attributeCode), 0, 30);
                    if ($nameValue['Value'] != '' && $nameValue['Value'] != 'Non applicabile') {
                        $attributeData[$mageAttrCode] = $nameValue['Value'];
                        array_push($superAttIds, $mageAttrCode);
                    }
                }
            }
            if (!empty($attributeData)) {
                $quictProData = $this->getQuickProductData($variation, $attributeData, $itemId, $count, $weight);
                array_push($associatePro, $quictProData);
                $count++;
            }
        }
        $response = ['super_att_ids' => array_unique($superAttIds), 'associate_pro' => $associatePro];
        return $response;
    }

   /**
    * getQuickProductData
    * @param array $variation
    * @param varchar $itemId
    * @param int $count
    * @param float $weight
    * @return array
    */
    private function getQuickProductData($variation, $attributeData, $itemId, $count, $weight)
    {
        if (isset($variation['StartPrice']) && isset($variation['StartPrice']['_'])) {
            $itemPrice = isset($variation['StartPrice']['_']) ? $variation['StartPrice']['_'] : $variation['StartPrice'];
            $currencyId = isset($variation['StartPrice']['currencyID']) ? $variation['StartPrice']['currencyID'] : '';
        } else {
            $itemPrice = isset($variation['_']) ? $variation['_'] : $variation['StartPrice'];
            $currencyId = isset($variation['currencyID']) ? $variation['currencyID'] : '';
        }

        $quictProData = [
            'status' => 1,
            'sku' => $itemId.'-'.$count,
            'price' => $itemPrice,
            'currency_id' => $currencyId,
            'qty' => (int) $variation['Quantity'] - (int) $variation['SellingStatus']['QuantitySold'],
            'is_in_stock' => (int) $variation['SellingStatus']['QuantitySold'] > 0 ? 1 : 0,
            'tax_class_id' => 0,
            'weight' => $weight,
            'visibility' => 1,
        ];

        foreach ($attributeData as $mageAttrCode => $value) {
            $quictProData[$mageAttrCode] = $this->getAttributeOptionId($mageAttrCode, $value);
        }
        return $quictProData;
    }

    /**
     * getAttribute
     * @param string $mageAttrCode
     * @param string $value
     * @return int
     */
    private function getAttributeOptionId($mageAttrCode, $value)
    {
        try {
            $attributeInfo = $this->attributeModel->create()->getCollection()
                                        ->addFieldToFilter('attribute_code', $mageAttrCode)
                                        ->setPageSize(1)->getFirstItem();
            $attribute = $this->attributeModel->create()->load($attributeInfo->getAttributeId());
            return $attribute->getSource()->getOptionId($value);
        } catch (\Exception $e) {
            $this->logger->info('getSuperAttrIds '.$e->getMessage());
        }
    }

    /**
     * manageProductData
     * @param array $data
     */
    private function getManageProductData($data)
    {
        $proCatSpec = [];
        $ebayCatId = $data['PrimaryCategory']['CategoryID'];
        $catMapData = $this->helper->getStoreBayCatMapData($ebayCatId, $this->ruleId);
        $eBayMageId = $catMapData ? $catMapData->getMageCatId() : $this->helper->config['default_cate'];
        //Custom Code
        $catMapData_2 = false;
        if (isset($data['SecondaryCategory']) && isset($data['SecondaryCategory']['CategoryID'])) {
            $ebayCatId_2 = $data['SecondaryCategory']['CategoryID'];
            $catMapData_2 = $this->helper->getStoreBayCatMapData($ebayCatId_2, $this->ruleId);
        }
        $eBayMageId_2 = $catMapData_2 ? $catMapData_2->getMageCatId() :$this->helper->config['default_cate'];

        if (isset($data['Variations'])) {
            $attr = $this->helper->createSuperAttrMagento($data['Variations'], $this->attributeSetId);
        }
        /* get Conditional attribute of product*/
        if ($catMapData && $catMapData->getProConditionAttr() != 'N/A') {
            $label = $data['ConditionID'].' for '.$data['ConditionDisplayName'];
            $conditionAttr = $this->helper->getAttributeOptValue($catMapData->getProConditionAttr(), $label);
            if ($conditionAttr && isset($conditionAttr['label'])) {
                $proCatSpec[$catMapData->getProConditionAttr()] = $conditionAttr['value'];
            } else {
                $ebayCatName = explode(':', $data['PrimaryCategory']['CategoryName']);
                $ebayCatName = $ebayCatName[count($ebayCatName) - 1];
                $this->helper->createProConditionAttr([$data['ConditionDisplayName']], $ebayCatName, $this->attributeSetId);
                $attributeCode = str_replace(' ', '_', $ebayCatName);
                $attributeCode = preg_replace('/[^A-Za-z0-9\_]/', '', $attributeCode);
                $mageAttrCode = substr('ebay_cond_cat_'.strtolower($attributeCode), 0, 30);
                $conditionAttr = $this->helper->getAttributeOptValue($mageAttrCode, $label);
                $proCatSpec[$catMapData->getProConditionAttr()] = $conditionAttr['value'];
            }
        }

        /**get Weight of product **/
        $weight = 0;
        if (isset($data['ShippingDetails']['CalculatedShippingRate'])
            && is_array($data['ShippingDetails']['CalculatedShippingRate'])) {
            foreach ($data['ShippingDetails']['CalculatedShippingRate'] as $weightData) {
                if (isset($weightData['unit']) && $weightData['unit'] == 'lbs') {
                    $weight = $weightData['_'];
                } elseif (isset($data['ShippingDetails']['CalculatedShippingRate']['any'])) {
                    preg_match("/<WeightMajor[^>]*>(.*?)<\\/WeightMajor>/si", $data['ShippingDetails']['CalculatedShippingRate']['any'], $match);
                    $weight = $match[1];
                }
            }
        }
        $weight = $weight ? $weight : 1;

        $responce = [
            'ebay_mage_id' => $eBayMageId.','.$eBayMageId_2,
            'pro_cat_spec' => $proCatSpec,
            'weight' => $weight,
            'attribute_set_id' => $this->attributeSetId
        ];

        return $responce;
    }

    /**
     * setImageData
     * @param array $wholedata
     * @param array $pictureDetails
     * @return array
     */
    private function setImageData($wholedata, $pictureDetails)
    {
        $imageArr = [];
        $defaultImage = '';
        $i = 0;
        $path = BP.'/pub/media/import/multiebaystoremageconnect/';
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        if ($pictureDetails['PhotoDisplay'] != 'None' && isset($pictureDetails['PictureURL'])) {
            if (!is_array($pictureDetails['PictureURL'])) {
                if ($i == 0) {
                    $defaultImage = $pictureDetails['PictureURL'];
                    $i ++;
                }
                array_push($imageArr, $pictureDetails['PictureURL']);
            } else {
                foreach ($pictureDetails['PictureURL'] as $value) {
                    if ($i == 0) {
                        $defaultImage = $value;
                        $i++;
                    }
                    array_push($imageArr, $value);
                }
            }
            $wholedata['image_data'] = ['default' => $defaultImage, 'images' => $imageArr];
        }
        return $wholedata;
    }

    /**
     * getItemSpecification
     * @param strint $itemId
     * @param int $attributeSetId
     * @param Ebay\eBaySOAP $client
     * @return array|false
     */
    private function getItemSpecification($itemId, $attributeSetId = 0, $client)
    {
        try {
            $params = [
                'Version'             => 659,
                'DetailLevel'       => 'ReturnAll',
                'ItemID'              => $itemId,
                'IncludeItemSpecifics' => true
            ];

            $results = $client->GetItem($params);

            if (isset($results->Ack) && $results->Ack == 'Success') {
                if (isset($results->Item->ItemSpecifics->NameValueList)) {
                    $specification = $results->Item->ItemSpecifics->NameValueList;
                    $specification = json_decode((json_encode($specification)), true);
                    $specification = isset($specification[0]) ? $specification : [0 => $specification];
                    $specification = $this->helper->createProSpecificationAttribute($specification, $attributeSetId);
                    return $specification;
                }
            }
            return false;
        } catch (\Exception $e) {
            $this->logger->info('getItemSpecification Error :- '. $e->getMessage());
            return false;
        }
    }
}
