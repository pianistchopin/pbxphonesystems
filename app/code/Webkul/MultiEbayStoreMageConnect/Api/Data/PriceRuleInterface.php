<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Api\Data;

interface PriceRuleInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ID = 'entity_id';
    const PRICE_FROM = 'price_from';
    const PRICE_TO = 'price_to';
    const OPERATION = 'operation';
    const OPERATION_TYPE = 'operation_type';
    const PRICE = 'price';
    const CREATED_AT = 'created_at';

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
    public function getPriceFrom();

   /**
    * set price.
    * @return $this
    */
    public function setPriceFrom($price);

   /**
    * Get price.
    * @return string
    */
    public function getPriceTo();

   /**
    * set price.
    * @return $this
    */
    public function setPriceTo($price);

   /**
    * Get price.
    * @return string
    */
    public function getPrice();

   /**
    * set price.
    * @return $this
    */
    public function setPrice($price);

   /**
    * Get operation.
    * @return string
    */
    public function getOperation();

   /**
    * set opeartion
    * @return $this
    */
    public function setOperation($action);

   /**
    * Get operation type.
    * @return string
    */
    public function getOperationType();

   /**
    * set operationType.
    * @return $this
    */
    public function setOperationType($operationType);

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
