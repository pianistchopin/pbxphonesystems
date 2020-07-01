<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Webkul\MultiEbayStoreMageConnect\Api\Data\ListingTemplateInterface;
use Magento\Framework\DataObject\IdentityInterface;

class ListingTemplate extends \Magento\Framework\Model\AbstractModel implements ListingTemplateInterface //, IdentityInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'wk_multiebay_listing_template';

    /**
     * @var string
     */
    protected $_cacheTag = 'wk_multiebay_listing_template';

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
        $this->_init('Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\ListingTemplate');
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
}
