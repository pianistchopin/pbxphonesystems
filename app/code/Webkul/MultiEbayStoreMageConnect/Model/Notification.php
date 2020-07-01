<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Ebay;

/**
 * Index class of EventNotification
 */
class Notification
{

    const INVENTORY_MANAGEMENT = 'inventory_management';

    public $logger;

    public $productMap;

    public $dataHelper;

    public $sellerId;

    public $context;

    public $manageRawData;
    /**
     * Dispatch method to ensure signature validation.
     */
    public function __call($method, $args)
    {
        $this->objectInitilize();
        $this->logger->info(" event method name ".$method);

        $method = substr($method, 0, -8);
        $this->logger->info(" method name after strip ".$method);
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $args);
        }
        $this->logger->info(" finished ");
    }

    public function objectInitilize()
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->logger = $objectManager->create('Webkul\MultiEbayStoreMageConnect\Logger\Logger');
            $this->productMap = $objectManager->create('Webkul\MultiEbayStoreMageConnect\Model\Productmap');
            $this->dataHelper = $objectManager->create('Webkul\MultiEbayStoreMageConnect\Helper\Data');
            $this->manageRawData = $objectManager->create('Webkul\MultiEbayStoreMageConnect\Helper\ManageRawData');
            $this->context = $objectManager->create('Magento\Framework\App\Action\Context');
        } catch (\Exception $e) {
            $this->logger->info(' Model Notification objectInitilize '. $e->getMessage());
        }
    }
    /**
     * Extract Signature for validation later
     * Can't validate here because we don't have access to the Timestamp.
     */
    public function RequesterCredentials($RequesterCredentials)
    {
        $this->NotificationSignature = $RequesterCredentials->NotificationSignature;
    }

    public function GetItem(
        $Timestamp,
        $Ack,
        $CorrelationID,
        $Version,
        $Build,
        $NotificationEventName,
        $RecipientUserID,
        $EIASToken,
        $Item
    ) {

        $this->logger->info(" Notification name ");
        $this->logger->info($NotificationEventName);
        $path = 'multiebaystoremageconnect/import_status/product_import_enable';
        $importEnable = $this->dataHelper->getConfigValue($path);

        $ItemID = $Item->ItemID;
        $ebayUserId = $Item->Seller->UserID;
        $this->logger->info(' item Id ' .$ItemID);
        $this->logger->info(' item Id' .$ebayUserId);
        $this->getSellerId($ebayUserId);
        $this->logger->info(' account id ' .$this->sellerId);
        if ($NotificationEventName == 'ItemRevised') {
            try {
                $mageProId = $this->isItemExist($ItemID);
                if ($mageProId) {
                    $this->logger->info(" revise data in Notification ");
                    $data = [0 => $Item];
                    $wholedata = $this->manageRawData->ManageProductRawData($data, $this->sellerId, null, false, true);
                    $this->logger->info(" revise whole data in Notification ");
                    $this->logger->info(json_encode($wholedata));
                    $wholedata['revise'] = 1;
                    $wholedata['id'] = $mageProId;
                    $this->processProductData($wholedata);
                    $this->logger->info(' revise product ');
                } elseif($importEnable) {
                    $this->logger->info(" new product data in Notification ");
                    $this->logger->info(json_encode($Item));
                    $data = [0 => $Item];
                    $wholedata = $this->manageRawData->ManageProductRawData($data, $this->sellerId, null, false, true);
                    $this->logger->info(" well format data ");
                    $this->logger->info(json_encode($wholedata));
                    $this->processProductData($wholedata);
                }
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
        }

        //if seller's single-quantity, fixed-price listing ends with a sale.
        if ($NotificationEventName == 'ItemSold') {
            $this->logger->info('ItemSold notification ');
            $client = $this->dataHelper->getEbayAPI($this->sellerId);

            if ($this->dataHelper->getSoldItemAction() === self::INVENTORY_MANAGEMENT) {
                $this->logger->info('INVENTORY_MANAGEMENT');
                $this->dataHelper->updateItemQtyAtMage($this->sellerId, $client, $ItemID);
            } else {
                $orderIds = $this->dataHelper->getOrderIdsByItemId($client, $ItemID);
                $this->logger->info('Model Notification : order Ids '. json_encode($orderIds));
                if (!empty($orderIds)) {
                    $this->dataHelper->addRealTimeOrderByOrderId($ebayUserId, $client, $orderIds);
                }
            }
        }

        if ($NotificationEventName == 'ItemClosed') {
            $mageProId = $this->isItemExist($ItemID);
            $this->dataHelper->ebayItemClosed($this->sellerId, $mageProId);
        }

        if ($NotificationEventName == 'ItemListed' && $importEnable) {
            $data = [0 => $Item];
            $wholedata = $this->manageRawData->ManageProductRawData($data, $this->sellerId, null, false, true);
            $this->processProductData($wholedata);
        }
        return true;
    }


    /**
     * if seller's multi-quantity, fixed-price listing sold.
     */
    public function GetItemTransactions(
        $Timestamp,
        $Ack,
        $CorrelationID,
        $Version,
        $Build,
        $NotificationEventName,
        $RecipientUserID,
        $EIASToken,
        $PaginationResult,
        $HasMoreTransactions,
        $TransactionsPerPage,
        $PageNumber,
        $ReturnedTransactionCountActual,
        $Item,
        $TransactionArray
    ) {
        return true;
    }

    /**
     * get ebay seller id
     *
     * @param int $userId
     * @return int
     */
    public function getSellerId($userId)
    {
        try {
            $this->sellerId = $this->dataHelper->getSellerIdByeBayUserId($userId);
        } catch (\Exception $e) {
            $this->logger->info('Model Notification getSellerId : '.$e->getMessage());
        }
    }

    /**
     * create ebay product at magento
     *
     * @param array $tempProData
     * @return void
     */
    public function processProductData($tempProData)
    {
        try {
            $request = $this->context->getRequest();
            $this->dataHelper->ruleId = $this->sellerId;
            $this->logger->info('Model notification processProductData : start new');
            if (($tempProData['type_id'] == 'simple') || (isset($tempProData['supperattr'])
            && count($tempProData['supperattr']) == 0)) {
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
                $result = $this->dataHelper->saveSimpleProduct($request);
            } else {
                foreach ($tempProData as $key => $value) {
                    $request->setParam($key, $value);
                }
                $result = $this->dataHelper->saveConfigProduct($request);
            }
            $this->logger->info('Model notification processProductData result : '.json_encode($result));
            if (isset($result['product_id']) && $result['product_id']) {
                $data = [
                    'ebay_pro_id' => $tempProData['sku'],
                    'name' => $tempProData['name'],
                    'rule_id' => $this->sellerId,
                    'price' => $tempProData['price'],
                    'product_type' => $tempProData['type_id'],
                ];

                $this->logger->info('Model notification processProductData : '.json_encode($result));
                if (isset($result['product_id']) && $result['product_id']) {
                    $this->logger->info(' inside : ');
                    if (!isset($tempProData['id'])) {
                        $data['magento_pro_id'] = $result['product_id'];
                        $data['mage_cat_id'] = $tempProData['category'][0];
                        $this->logger->info(' going to saved : '.json_encode($data));
                        $this->productMap->setData($data)->save();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('notification processProductData  Exception: '.$e->getMessage());
        }
    }

    /**
     * check item exist or not at magento
     *
     * @param int $itemId
     * @return boolean
     */
    public function isItemExist($itemId)
    {
        try {
            $productMapCol = $this->productMap->getCollection()
                    ->addFieldToFilter('ebay_pro_id', ['eq'=>$itemId]);
            if ($productMapCol->getSize()) {
                foreach ($productMapCol as $mappedRecord) {
                    return $mappedRecord->getMagentoProId();
                }
            }
            return false;
        } catch (\Exception $e) {
            $this->logger->info('Model notification isItemExist : '.$e->getMessage());
        }
    }
}
