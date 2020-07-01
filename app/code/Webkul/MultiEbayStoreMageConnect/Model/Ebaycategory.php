<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\EbaycategoryInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Ebaycategory extends \Magento\Framework\Model\AbstractModel implements EbaycategoryInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'wk_multiebay_categories';

    /**
     * @var string
     */
    protected $_cacheTag = 'wk_multiebay_categories';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'wk_multiebay_categories';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebaycategory');
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
     * Get EbayCatParentid.
     *
     * @return varchar
     */
    public function getEbayCatParentid()
    {
        return $this->getData(self::EBAY_CAT_PARENTID);
    }

    /**
     * Set EbayCatParentid.
     */
    public function setEbayCatParentid($ebayCatParentid)
    {
        return $this->setData(self::EBAY_CAT_PARENTID, $ebayCatParentid);
    }

    /**
     * Get EbayCatName.
     *
     * @return varchar
     */
    public function getEbayCatName()
    {
        return $this->getData(self::EBAY_CAT_NAME);
    }

    /**
     * Set EbayCatName.
     */
    public function setEbayCatName($ebayCatName)
    {
        return $this->setData(self::EBAY_CAT_NAME, $ebayCatName);
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
