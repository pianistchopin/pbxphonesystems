<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\ImportedtmpproductInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Importedtmpproduct extends \Magento\Framework\Model\AbstractModel implements ImportedtmpproductInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'wk_multiebay_tempebay';

    /**
     * @var string
     */
    public $_cacheTag = 'wk_multiebay_tempebay';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    public $_eventPrefix = 'wk_multiebay_tempebay';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Importedtmpproduct');
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
    public function setId($entityId)
    {
        return $this->setData(self::ID, $entityId);
    }

    /**
     * Get ItemId.
     *
     * @return varchar
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * Set ItemId.
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * Get ProductData.
     *
     * @return varchar
     */
    public function getProductData()
    {
        return $this->getData(self::PRODUCT_DATA);
    }

    /**
     * Set ProductData.
     */
    public function setProductData($productData)
    {
        return $this->setData(self::PRODUCT_DATA, $productData);
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
