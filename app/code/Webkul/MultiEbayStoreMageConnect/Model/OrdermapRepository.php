<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\OrdermapInterface;
use Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ordermap\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class OrdermapRepository implements \Webkul\MultiEbayStoreMageConnect\Api\OrdermapRepositoryInterface
{
    /**
     * resource model
     * @var \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategory
     */
    protected $_resourceModel;

    public function __construct(
        OrdermapFactory $orderMapFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ordermap\CollectionFactory $collectionFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ordermap $resourceModel
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_orderMapFactory = $orderMapFactory;
        $this->_collectionFactory = $collectionFactory;
    }
    
    /**
     * get ebay sync  collection of product by ebay rule id
     * @return object
     */
    public function getCollectionByRuleId($ruleId)
    {
        $synOrderCollection =  $this->_orderMapFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'rule_id',
                [
                    'eq'=>$ruleId
                ]
            );
        return $synOrderCollection;
    }

    /**
     * get record by ebay order id
     * @param  int $ebayOrderId
     * @return object
     */
    public function getRecordByEbayOrderId($ebayOrderId)
    {
        $synOrderCollection =  $this->_orderMapFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'ebay_order_id',
                [
                    'eq'=>$ebayOrderId
                ]
            );
        return $synOrderCollection;
    }

    /**
     * get record by mage order id
     * @param  int $mageOrderId
     * @return object
     */
    public function getRecordByMageOrderId($mageOrderId)
    {
        $synOrderCollection =  $this->_orderMapFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'mage_order_id',
                [
                    'eq'=>$mageOrderId
                ]
            );
        return $synOrderCollection;
    }

    /**
     * get collection by entity ids
     * @param  array $entityIds
     * @return object
     */
    public function getCollectionByIds(array $entityIds)
    {
        $synOrderCollection =  $this->_orderMapFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'entity_id',
                [
                    'in'=>$entityIds
                ]
            );
        return $synOrderCollection;
    }

    /**
     * get collection by account id and order id
     * @param  array $entityIds
     * @return object
     */
    public function getByAccountIdnOrderId($accountId, $orderId)
    {
        $synOrderCollection =  $this->_orderMapFactory->create()
                            ->getCollection()
                            ->addFieldToFilter(
                                'rule_id',
                                [
                                    'eq'=>$accountId
                                ]
                            )->addFieldToFilter(
                                'ebay_order_id',
                                [
                                    'eq'=>$orderId
                                ]
                            );
        return $synOrderCollection;
    }
}
