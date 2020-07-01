<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\ImportedtmpproductInterface;
use Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Importedtmpproduct\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ImportedtmpproductRepository implements \Webkul\MultiEbayStoreMageConnect\Api\ImportedtmpproductRepositoryInterface
{
    /**
     * resource model
     * @var \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategory
     */
    protected $_resourceModel;

    public function __construct(
        ImportedtmpproductFactory $importedTmpProductFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Importedtmpproduct\CollectionFactory $collectionFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Importedtmpproduct $resourceModel
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_importedTmpProductFactory = $importedTmpProductFactory;
        $this->_collectionFactory = $collectionFactory;
    }
    
    /**
     * get a record by item id and product type
     * @param  string $productType
     * @param  int $itemId
     * @return object
     */
    public function getRecordByItemIdnProductType($productType, $itemId)
    {
        $temItemRecord = $this->_importedTmpProductFactory
                    ->create()->getCollection()
                    ->addFieldToFilter(
                        'item_type',
                        $productType
                    )->addFieldToFilter(
                        'item_id',
                        $itemId
                    );//->getFirstItem();
        return $temItemRecord;
    }

    /**
     * get tempdate collection by rule id and product type
     * @param  string $productType
     * @param  int $ruleId
     * @return object
     */
    public function getCollectionByProductTypeAndRuleId($productType, $ruleId)
    {
        $temItemRecord = $this->_importedTmpProductFactory
                    ->create()->getCollection()
                    ->addFieldToFilter(
                        'item_type',
                        $productType
                    )->addFieldToFilter(
                        'rule_id',
                        $ruleId
                    );
        return $temItemRecord;
    }
}
