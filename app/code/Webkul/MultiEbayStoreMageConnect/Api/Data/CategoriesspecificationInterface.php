<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Api\Data;

interface CategoriesspecificationInterface
{
    /**
     * Constants for keys of data array.
     *Identical to the name of the getter in snake case.
     */
    const ID = 'entity_id';
    const EBAY_CATEGORY_ID = 'ebay_category_id';
    const EBAY_SPECIFICATION_NAME = 'ebay_specification_name';
    const MAGE_PRODUCT_ATTRIBUTE_CODE = 'mage_product_attribute_code';
    const CREATED_AT = 'created';

    /**
     * Get ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * set ID.
     *
     * @return $this
     */
    public function setId($entityId);

   /**
    * Get EbayCategoryId.
    *
    * @return string
    */
    public function getEbayCategoryId();

   /**
    * set EbayCategoryId.
    *
    * @return $this
    */
    public function setEbayCategoryId($ebayCategoryId);

   /**
    * Get EbaySpecificationName.
    *
    * @return string
    */
    public function getEbaySpecificationName();

   /**
    * set EbaySpecificationName.
    *
    * @return $this
    */
    public function setEbaySpecificationName($ebaySpecificationName);

   /**
    * Get MageProductAttributeCode.
    *
    * @return string
    */
    public function getMageProductAttributeCode();

   /**
    * set MageProductAttributeCode.
    *
    * @return $this
    */
    public function setMageProductAttributeCode($mageProductAttributeCode);

   /**
    * Get CreatedAt.
    *
    * @return string
    */
    public function getCreatedAt();

   /**
    * set CreatedAt.
    *
    * @return $this
    */
    public function setCreatedAt($createdAt);
}
