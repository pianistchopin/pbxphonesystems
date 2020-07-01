<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Api\Data;

interface OrdermapInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ID = 'entity_id';
    const EBAY_ORDER_ID = 'ebay_order_id';
    const MAGE_ORDER_ID = 'mage_order_id';
    const STATUS = 'status';
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
    * Get EbayOrderId.
    * @return string
    */
    public function getEbayOrderId();

   /**
    * set EbayOrderId.
    * @return $this
    */
    public function setEbayOrderId($ebayOrderId);

   /**
    * Get MageOrderId.
    * @return string
    */
    public function getMageOrderId();

   /**
    * set MageOrderId.
    * @return $this
    */
    public function setMageOrderId($mageOrderId);

   /**
    * Get Status.
    * @return string
    */
    public function getStatus();

   /**
    * set Status.
    * @return $this
    */
    public function setStatus($status);

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
