<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Api\Data;

interface ImportedtmpproductInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ID = 'entity_id';
    const ITEM_ID = 'item_id';
    const PRODUCT_DATA = 'product_data';
    const CREATED_AT = 'created_at';

    /**
     * Get ID.
     * @return int|null
     */
    public function getId();

    /**
     * set ID.
     * @return $this
     */
    public function setId($id);

   /**
    * Get ItemId.
    * @return string
    */
    public function getItemId();

   /**
    * set ItemId.
    * @return $this
    */
    public function setItemId($itemId);

   /**
    * Get ProductData.
    * @return string
    */
    public function getProductData();

   /**
    * set ProductData.
    * @return $this
    */
    public function setProductData($productData);

    /**
     * Get CreatedAt.
     * @return string
     */
    public function getCreatedAt();

   /**
    * set CreatedAt.
    * @return $this
    */
    public function setCreatedAt($createdAt);
}
