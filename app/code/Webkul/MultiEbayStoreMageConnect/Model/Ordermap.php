<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\OrdermapInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Ordermap extends \Magento\Framework\Model\AbstractModel implements OrdermapInterface //, IdentityInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'wk_multiebaysynchronize_order';

    /**
     * @var string
     */
    protected $_cacheTag = 'wk_multiebaysynchronize_order';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'wk_multiebaysynchronize_order';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ordermap');
    }
    /**
     * Get Id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set Id.
     */
    public function setId($entityId)
    {
        return $this->setData(self::ID, $entityId);
    }

    /**
     * Get EbayOrderId.
     *
     * @return varchar
     */
    public function getEbayOrderId()
    {
        return $this->getData(self::EBAY_ORDER_ID);
    }

    /**
     * Set EbayOrderId.
     */
    public function setEbayOrderId($ebayOrderId)
    {
        return $this->setData(self::EBAY_ORDER_ID, $ebayOrderId);
    }

    /**
     * Get MageOrderId.
     *
     * @return varchar
     */
    public function getMageOrderId()
    {
        return $this->getData(self::MAGE_ORDER_ID);
    }

    /**
     * Set MageOrderId.
     */
    public function setMageOrderId($mageOrderId)
    {
        return $this->setData(self::MAGE_ORDER_ID, $mageOrderId);
    }

    /**
     * Get Status.
     *
     * @return varchar
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set Status.
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
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
