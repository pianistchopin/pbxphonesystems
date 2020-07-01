<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Api\Data;

interface EbaycategoryInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ID = 'id';
    const EBAY_CAT_ID = 'ebay_cat_id';
    const EBAY_CAT_PARENTID = 'ebay_cat_parentid';
    const EBAY_CAT_NAME = 'ebay_cat_name';
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
    * Get EbayCatParentid.
    *
    * @return string
    */
    public function getEbayCatParentid();

   /**
    * set EbayCatParentid.
    *
    * @return $this
    */
    public function setEbayCatParentid($ebayCatParentid);

   /**
    * Get EbayCatName.
    *
    * @return string
    */
    public function getEbayCatName();

   /**
    * set EbayCatName.
    *
    * @return $this
    */
    public function setEbayCatName($ebayCatName);

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
