<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\EbaycategorymapInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Ebaycategorymap extends \Magento\Framework\Model\AbstractModel implements EbaycategorymapInterface //, IdentityInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'wk_multiebaysynchronize_category';

    /**
     * @var string
     */
    protected $_cacheTag = 'wk_multiebaysynchronize_category';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'wk_multiebaysynchronize_category';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategorymap');
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
     * Get EbayCatId.
     *
     * @return varchar
     */
    public function getEbayCatId()
    {
        return $this->getData(self::EBAY_CAT_ID);
    }

    /**
     * Set EbayCatId.
     */
    public function setEbayCatId($ebayCatId)
    {
        return $this->setData(self::EBAY_CAT_ID, $ebayCatId);
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
     * Get ProConditionAttr.
     *
     * @return varchar
     */
    public function getProConditionAttr()
    {
        return $this->getData(self::PRO_CONDITION_ATTR);
    }

    /**
     * Set ProConditionAttr.
     */
    public function setProConditionAttr($proConditionAttr)
    {
        return $this->setData(self::PRO_CONDITION_ATTR, $proConditionAttr);
    }

    /**
     * Get VariationsEnabled.
     *
     * @return varchar
     */
    public function getVariationsEnabled()
    {
        return $this->getData(self::VARIATIONS_ENABLED);
    }

    /**
     * Set VariationsEnabled.
     */
    public function setVariationsEnabled($variationsEnabled)
    {
        return $this->setData(self::VARIATIONS_ENABLED, $variationsEnabled);
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
