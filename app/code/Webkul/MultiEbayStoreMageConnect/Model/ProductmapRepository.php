<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\ProductmapInterface;
use Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Productmap\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ProductmapRepository implements \Webkul\MultiEbayStoreMageConnect\Api\ProductmapRepositoryInterface
{
    /**
     * resource model
     * @var \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Productmap
     */
    protected $_resourceModel;

    public function __construct(
        ProductmapFactory $productmapFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Productmap\CollectionFactory $collectionFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Productmap $resourceModel
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_productmapFactory = $productmapFactory;
        $this->_collectionFactory = $collectionFactory;
    }
    
    /**
     * get ebay sync  collection of product by ebay rule id
     * @return object
     */
    public function getCollectionByRuleId($ruleId)
    {
        $synProductCollection =  $this->_productmapFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'rule_id',
                [
                    'eq'=>$ruleId
                ]
            );
        return $synProductCollection;
    }

    /**
     * get collection by ebay product id
     * @param  int $ebayProductId
     * @return object
     */
    public function getRecordByEbayProductId($ebayProductId)
    {
        $synProductCollection =  $this->_productmapFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'ebay_pro_id',
                [
                    'eq'=>$ebayProductId
                ]
            );
        return $synProductCollection;
    }

    /**
     * get record by magento product id
     * @param  int $mageProductId
     * @return object
     */
    public function getRecordByMageProductId($mageProductId)
    {
        $synProductCollection =  $this->_productmapFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'magento_pro_id',
                [
                    'eq'=>$mageProductId
                ]
            );
        return $synProductCollection;
    }

    /**
     * get collection by entity ids
     * @param  array $entityIds
     * @return object
     */
    public function getCollectionByIds(array $entityIds)
    {
        $synProductCollection =  $this->_productmapFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'entity_id',
                [
                    'in'=>$entityIds
                ]
            );
        return $synProductCollection;
    }
}
