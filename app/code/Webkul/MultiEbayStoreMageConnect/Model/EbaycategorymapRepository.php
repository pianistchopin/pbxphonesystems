<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\EbaycategorymapInterface;
use Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategorymap\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class EbaycategorymapRepository implements \Webkul\MultiEbayStoreMageConnect\Api\EbaycategorymapRepositoryInterface
{
    /**
     * resource model
     * @var \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\EbaycategorymapFactory
     */
    protected $_resourceModel;

    /**
     * @param EbaycategorymapFactory                                                                  $ebayCategoryMapFactory
     * @param \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategorymap\CollectionFactory $collectionFactory
     * @param \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategorymap                   $resourceModel
     */
    public function __construct(
        EbaycategorymapFactory $ebayCategoryMapFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategorymap\CollectionFactory $collectionFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategorymap $resourceModel
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_ebayCategoryMapFactory = $ebayCategoryMapFactory;
        $this->_collectionFactory = $collectionFactory;
    }
    
    /**
     * get ebay mapped category collection by rule id
     * @return object
     */
    public function getCollectionByRuleId($ruleId)
    {
        $mappedCateColl = $this->_ebayCategoryMapFactory
                        ->create()->getCollection()
                        ->addFieldToFilter(
                            'rule_id',
                            [
                                'eq'=>$ruleId
                            ]
                        );
        return $mappedCateColl;
    }

    /**
     * get collection by mage category id
     * @param  int $mageCateId
     * @return object
     */
    public function getCollectionByMageCateIdnRuleId($mageCateId, $ruleId)
    {
        $mappedCateColl = $this->_ebayCategoryMapFactory
                        ->create()->getCollection()
                        ->addFieldToFilter(
                            'rule_id',
                            [
                                'eq'=>$ruleId
                            ]
                        )->addFieldToFilter(
                            'mage_cat_id',
                            [
                                'eq'=>$mageCateId
                            ]
                        );
        return $mappedCateColl;
    }

    /**
     * get collection by entity ids
     * @param  array $entityIds
     * @return object
     */
    public function getCollectionByIds(array $entityIds)
    {
        $synCateCollection =  $this->_ebayCategoryMapFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'entity_id',
                [
                    'in'=>$entityIds
                ]
            );
        return $synCateCollection;
    }

    /**
     * get record by magento category id
     * @param  int $mageProductId
     * @return object
     */
    public function getCollectionByMageCateIdsnRuleId($mageCateIds, $ruleId)
    {
        $mappedCateColl = $this->_ebayCategoryMapFactory
                        ->create()->getCollection()
                        ->addFieldToFilter(
                            'mage_cat_id',
                            [
                                'in'=>$mageCateIds
                            ]
                        )->addFieldToFilter(
                            'rule_id',
                            [
                                'eq'=>$ruleId
                            ]
                        );
        return $mappedCateColl;
    }

    /**
     * get collection by rule id and ebay cate id
     * @param  int $eBayCateId
     * @param  int $ruleId
     * @return object
     */
    public function getCollectionByeBayCateIdnRuleId($eBayCateId, $ruleId)
    {
        $mappedCateColl = $this->_ebayCategoryMapFactory
                        ->create()->getCollection()
                        ->addFieldToFilter(
                            'rule_id',
                            [
                                'eq'=>$ruleId
                            ]
                        )->addFieldToFilter(
                            'ebay_cat_id',
                            [
                                'eq'=>$eBayCateId
                            ]
                        );
        return $mappedCateColl;
    }
}
