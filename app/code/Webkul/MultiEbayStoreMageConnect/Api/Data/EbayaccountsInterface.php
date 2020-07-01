<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Api\Data;

interface EbayaccountsInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ID = 'entity_id';
    const ATTRIBUTE_SET_ID = 'attribute_set_id';
    const GLOBAL_SITE = 'global_site';
    const EBAY_USER_ID = 'ebay_user_id';
    const EBAY_AUTHENTICATION_TOKEN = 'ebay_authentication_token';
    const EBAY_DEVELOPER_ID = 'ebay_developer_id';
    const EBAY_APPLICATION_ID = 'ebay_application_id';
    const EBAY_CERTIFICATION_ID = 'ebay_certification_id';
    const SHOP_POSTAL_CODE = 'shop_postal_code';

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
     * Get attribute set id
     * @return int
     */
    public function getAttributeSetId();

    /**
     * set attribute set id
     * @return $this
     */
    public function setAttributeId($attributeSetId);

    /**
     * Get global site
     * @return string
     */
    public function getGlobalSite();

    /**
     * set global site
     * @return $this
     */
    public function setGlobalSite($globalSite);

    /**
     * Get ebay user id
     * @return string
     */
    public function getEbayUserId();

    /**
     * set ebay user id
     * @return $this
     */
    public function setEbayUserId($ebayUserId);

    /**
     * Get ebay authentication token .
     * @return string
     */
    public function getEbayAuthenticationToken();

    /**
     * set ebay authentication token .
     * @return $this
     */
    public function setEbayAuthenticationToken($ebayAuthenticationToken);

    /**
     * Get ebay developer id.
     * @return string
     */
    public function getEbayDeveloperId();

    /**
     * set ebay developer id.
     * @return $this
     */
    public function setEbayDeveloperId($ebayDeveloperId);

    /**
     * Get ebay application id.
     * @return string
     */
    public function getEbayApplicationId();

    /**
     * set ebay application id.
     * @return $this
     */
    public function setEbayApplicationId($ebayApplicationId);

    /**
     * Get ebay certification id.
     * @return string
     */
    public function getEbayCertificationId();

    /**
     * set ebay certification id.
     * @return $this
     */
    public function setEbayCertificationId($ebayCertificationId);

    /**
     * Get ebay shop postal code.
     * @return string
     */
    public function getShopPostalCode();

    /**
     * set ebay shop postal code.
     * @return $this
     */
    public function setShopPostalCode($ebayShopPostalCode);
}
