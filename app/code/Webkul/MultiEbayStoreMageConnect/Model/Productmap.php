<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\ProductmapInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Productmap extends \Magento\Framework\Model\AbstractModel implements ProductmapInterface //, IdentityInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'wk_multiebaysynchronize_product';

    /**
     * @var string
     */
    protected $_cacheTag = 'wk_multiebaysynchronize_product';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'wk_multiebaysynchronize_product';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Productmap');
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
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get ebayProId.
     *
     * @return varchar
     */
    public function getEbayProId()
    {
        return $this->getData(self::EBAY_PRO_ID);
    }

    /**
     * Set EbayProId.
     */
    public function setEbayProId($ebayProId)
    {
        return $this->setData(self::EBAY_PRO_ID, $ebayProId);
    }

    /**
     * Get Name.
     *
     * @return varchar
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set Name.
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get ProductType.
     *
     * @return varchar
     */
    public function getProductType()
    {
        return $this->getData(self::PRODUCT_TYPE);
    }

    /**
     * Set ProductType.
     */
    public function setProductType($productType)
    {
        return $this->setData(self::PRODUCT_TYPE, $productType);
    }

    /**
     * Get MagentoProId.
     *
     * @return varchar
     */
    public function getMagentoProId()
    {
        return $this->getData(self::MAGENTO_PRO_ID);
    }

    /**
     * Set MagentoProId.
     */
    public function setMagentoProId($magentoProId)
    {
        return $this->setData(self::MAGENTO_PRO_ID, $magentoProId);
    }

    /**
     * Get MageCatId.
     *
     * @return varchar
     */
    public function getMageCatId()
    {
        return $this->getData(self::MAGE_CAT_ID);
    }

    /**
     * Set MageCatId.
     */
    public function setMageCatId($mageCatId)
    {
        return $this->setData(self::MAGE_CAT_ID, $mageCatId);
    }

    /**
     * Get ChangeStatus.
     *
     * @return varchar
     */
    public function getChangeStatus()
    {
        return $this->getData(self::CHANGE_STATUS);
    }

    /**
     * Set ChangeStatus.
     */
    public function setChangeStatus($changeStatus)
    {
        return $this->setData(self::CHANGE_STATUS, $changeStatus);
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

    /**
     * Get rule id.
     *
     * @return int
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * Set rule id.
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }
}
