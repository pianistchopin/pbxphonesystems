<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Api\Data;

interface ProductmapInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ID = 'entity_id';
    const EBAY_PRO_ID = 'ebay_pro_id';
    const NAME = 'name';
    const PRODUCT_TYPE = 'product_type';
    const MAGENTO_PRO_ID = 'magento_pro_id';
    const MAGE_CAT_ID = 'mage_cat_id';
    const CHANGE_STATUS = 'change_status';
    const CREATED_AT = 'created';
    const RULE_ID = "rule_id";

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
     * Get EbayProId.
     * @return string
     */
    public function getEbayProId();

    /**
     * set EbayProId.
     * @return $this
     */
    public function setEbayProId($ebayProId);

    /**
     * Get Name.
     * @return string
     */
    public function getName();

    /**
     * set Name.
     * @return $this
     */
    public function setName($name);

    /**
     * Get ProductType.
     * @return string
     */
    public function getProductType();

    /**
     * set ProductType.
     * @return $this
     */
    public function setProductType($productType);

    /**
     * Get MagentoProId.
     * @return string
     */
    public function getMagentoProId();

    /**
     * set MagentoProId.
     * @return $this
     */
    public function setMagentoProId($magentoProId);

    /**
     * Get MageCatId.
     * @return string
     */
    public function getMageCatId();

    /**
     * set MageCatId.
     * @return $this
     */
    public function setMageCatId($mageCatId);

    /**
     * Get ChangeStatus.
     * @return string
     */
    public function getChangeStatus();

    /**
     * set ChangeStatus.
     * @return $this
     */
    public function setChangeStatus($changeStatus);

    /**
     * Get CreatedAt.
     * @return string
     */
    public function getCreatedAt();

    /**
     * set CreatedAt.
     * @return $this
     */
    public function setCreatedAt($created);

    /**
     * Get rule id.
     *
     * @return int
     */
    public function getRuleId();

    /**
     * Set rule id.
     */
    public function setRuleId($ruleId);
}
