<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\CategoriesspecificationInterface;
use Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Categoriesspecification\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CategoriesspecificationRepository implements \Webkul\MultiEbayStoreMageConnect\Api\CategoriesspecificationRepositoryInterface
{
    /**
     * resource model
     * @var \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Categoriesspecification
     */
    protected $_resourceModel;

    public function __construct(
        CategoriesspecificationFactory $categoriesSpecificationFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Categoriesspecification\CollectionFactory $collectionFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Categoriesspecification $resourceModel
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_categoriesSpecification = $categoriesSpecificationFactory;
        $this->_collectionFactory = $collectionFactory;
    }
    
    /**
     * get collection by ebay category id
     * @param  int $ebayCateId
     * @return object
     */
    public function getCollectionByeBayCatId($ebayCateId)
    {
        $cateSpecification = $this->_collectionFactory
                            ->create()
                            ->addFieldToFilter(
                                'ebay_category_id',
                                ['eq'=>$ebayCateId]
                            );
        return $cateSpecification;
    }
}
