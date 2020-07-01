<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\EbayaccountsInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Ebayaccounts extends \Magento\Framework\Model\AbstractModel implements EbayaccountsInterface //, IdentityInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'wk_multiebay_seller_details';

    /**
     * @var string
     */
    protected $_cacheTag = 'wk_multiebay_seller_details';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'wk_multiebay_seller_details';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebayaccounts');
    }

    /**
     * Get ID.
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * set ID.
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get attribute set id
     * @return int
     */
    public function getAttributeSetId()
    {
        return $this->getData(self::ATTRIBUTE_SET_ID);
    }

    /**
     * set attribute set id
     * @return $this
     */
    public function setAttributeId($attributeSetId)
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    /**
     * Get global site
     * @return string
     */
    public function getGlobalSite()
    {
        return $this->getData(self::GLOBAL_SITE);
    }

    /**
     * set global site
     * @return $this
     */
    public function setGlobalSite($globalSite)
    {
        return $this->setData(self::GLOBAL_SITE, $globalSite);
    }

    /**
     * Get ebay user id
     * @return string
     */
    public function getEbayUserId()
    {
        return $this->getData(self::EBAY_USER_ID);
    }

    /**
     * set ebay user id
     * @return $this
     */
    public function setEbayUserId($ebayUserId)
    {
        return $this->setData(self::EBAY_USER_ID, $ebayUserId);
    }

    /**
     * Get ebay authentication token .
     * @return string
     */
    public function getEbayAuthenticationToken()
    {
        return $this->getData(self::EBAY_AUTHENTICATION_TOKEN);
    }

    /**
     * set ebay authentication token .
     * @return $this
     */
    public function setEbayAuthenticationToken($ebayAuthenticationToken)
    {
        return $this->setData(self::EBAY_AUTHENTICATION_TOKEN, $ebayAuthenticationToken);
    }

    /**
     * Get ebay developer id.
     * @return string
     */
    public function getEbayDeveloperId()
    {
        return $this->getData(self::EBAY_DEVELOPER_ID);
    }

    /**
     * set ebay developer id.
     * @return $this
     */
    public function setEbayDeveloperId($ebayDeveloperId)
    {
        return $this->setData(self::EBAY_DEVELOPER_ID, $ebayDeveloperId);
    }

    /**
     * Get ebay application id.
     * @return string
     */
    public function getEbayApplicationId()
    {
        return $this->getData(self::EBAY_APPLICATION_ID);
    }

    /**
     * set ebay application id.
     * @return $this
     */
    public function setEbayApplicationId($ebayApplicationId)
    {
        return $this->setData(self::EBAY_APPLICATION_ID, $ebayApplicationId);
    }

    /**
     * Get ebay certification id.
     * @return string
     */
    public function getEbayCertificationId()
    {
        return $this->getData(self::EBAY_CERTIFICATION_ID);
    }

    /**
     * set ebay certification id.
     * @return $this
     */
    public function setEbayCertificationId($ebayCertificationId)
    {
        return $this->setData(self::EBAY_CERTIFICATION_ID, $ebayCertificationId);
    }

    /**
     * Get ebay shop postal code.
     * @return string
     */
    public function getShopPostalCode()
    {
        return $this->getData(self::SHOP_POSTAL_CODE);
    }

    /**
     * set ebay shop postal code.
     * @return $this
     */
    public function setShopPostalCode($ebayShopPostalCode)
    {
        return $this->setData(self::SHOP_POSTAL_CODE, $ebayShopPostalCode);
    }
}
