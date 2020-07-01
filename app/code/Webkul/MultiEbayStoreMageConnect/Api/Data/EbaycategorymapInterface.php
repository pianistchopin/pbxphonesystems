<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Api\Data;

interface EbaycategorymapInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ID = 'id';
    const EBAY_CAT_ID = 'ebay_cat_id';
    const MAGE_CAT_ID = 'mage_cat_id';
    const PRO_CONDITION_ATTR = 'pro_condition_attr';
    const VARIATIONS_ENABLED = 'variations_enabled';
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
    public function setId($id);

   /**
    * Get EbayCatId.
    *
    * @return string
    */
    public function getEbayCatId();

   /**
    * set EbayCatId.
    *
    * @return $this
    */
    public function setEbayCatId($ebayCatId);

   /**
    * Get MageCatId.
    *
    * @return string
    */
    public function getMageCatId();

   /**
    * set MageCatId.
    *
    * @return $this
    */
    public function setMageCatId($mageCatId);

   /**
    * Get ProConditionAttr.
    *
    * @return string
    */
    public function getProConditionAttr();

   /**
    * set ProConditionAttr.
    *
    * @return $this
    */
    public function setProConditionAttr($proConditionAttr);

   /**
    * Get VariationsEnabled.
    *
    * @return string
    */
    public function getVariationsEnabled();

   /**
    * set VariationsEnabled.
    *
    * @return $this
    */
    public function setVariationsEnabled($variationsEnabled);

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
