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
use Magento\Framework\DataObject\IdentityInterface;

class Categoriesspecification extends \Magento\Framework\Model\AbstractModel implements CategoriesspecificationInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'ebaysynchronize_specification_category';

    /**
     * @var string
     */
    protected $_cacheTag = 'ebaysynchronize_specification_category';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'ebaysynchronize_specification_category';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Categoriesspecification');
    }
    /**
     * Get EntityId.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set EntityId.
     */
    public function setId($entityId)
    {
        return $this->setData(self::ID, $entityId);
    }

    /**
     * Get EbayCategoryId.
     *
     * @return varchar
     */
    public function getEbayCategoryId()
    {
        return $this->getData(self::EBAY_CATEGORY_ID);
    }

    /**
     * Set EbayCategoryId.
     */
    public function setEbayCategoryId($ebayCategoryId)
    {
        return $this->setData(self::EBAY_CATEGORY_ID, $ebayCategoryId);
    }

    /**
     * Get eBaySpecificationName.
     *
     * @return varchar
     */
    public function getEbaySpecificationName()
    {
        return $this->getData(self::EBAY_SPECIFICATION_NAME);
    }

    /**
     * Set eBaySpecificationName.
     */
    public function setEbaySpecificationName($ebaySpecificationName)
    {
        return $this->setData(self::EBAY_SPECIFICATION_NAME, $ebaySpecificationName);
    }

    /**
     * Get MageProductAttributeCode.
     *
     * @return varchar
     */
    public function getMageProductAttributeCode()
    {
        return $this->getData(self::MAGE_PRODUCT_ATTRIBUTE_CODE);
    }

    /**
     * Set MageProductAttributeCode.
     */
    public function setMageProductAttributeCode($mageProductAttributeCode)
    {
        return $this->setData(self::MAGE_PRODUCT_ATTRIBUTE_CODE, $mageProductAttributeCode);
    }

    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set CreatedAt.
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
