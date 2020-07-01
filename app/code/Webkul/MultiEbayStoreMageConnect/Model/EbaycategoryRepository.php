<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\EbaycategoryInterface;
use Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategory\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class EbaycategoryRepository implements \Webkul\MultiEbayStoreMageConnect\Api\EbaycategoryRepositoryInterface
{
    /**
     * resource model
     * @var \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategory
     */
    protected $_resourceModel;

    public function __construct(
        EbaycategoryFactory $ebayCategoryFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategory\CollectionFactory $collectionFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategory $resourceModel
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_ebayCategoryFactory = $ebayCategoryFactory;
        $this->_collectionFactory = $collectionFactory;
    }
    
    /**
     * get ebay category collection by ebay category id
     * @return object
     */
    public function getCollectionByEbayCateId($ebayCateId)
    {
        $ebayExistCatColl = $this->_ebayCategoryFactory->create()->getCollection()
            ->addFieldToFilter(
                'ebay_cat_id',
                ['eq' => $ebayCateId]
            );
        return $ebayExistCatColl;
    }

    /**
     * get Collection by ebay parent id
     * @param  int $eBayParentId
     * @return object
     */
    public function getCollectionByeBayCateParentId($eBayParentId)
    {
        $ebayExistCatColl = $this->_ebayCategoryFactory->create()->getCollection()
            ->addFieldToFilter(
                'ebay_cat_parentid',
                ['eq' => $eBayParentId]
            )->addFieldToFilter(
                'ebay_cat_id',
                ['neq' => $eBayParentId]
            );
        return $ebayExistCatColl;
    }
}
