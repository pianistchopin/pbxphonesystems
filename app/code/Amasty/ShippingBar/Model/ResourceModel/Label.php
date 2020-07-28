<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Model\ResourceModel;

use Amasty\ShippingBar\Api\Data\LabelInterface;
use Magento\Framework\DB\Select;

class Label extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'amasty_shipbar_profile_label';

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _construct() //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $this->_init(self::TABLE_NAME, LabelInterface::LABEL_ID);
    }

    /**
     * @param int $profileId
     * @param int|null $storeId
     *
     * @return array
     */
    public function getDataByProfile($profileId, $storeId = null)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), [LabelInterface::STORE_ID, LabelInterface::ACTION, LabelInterface::LABEL]);

        if ($storeId) {
            $select->reset(Select::COLUMNS);
            $select->columns([LabelInterface::ACTION, LabelInterface::LABEL]);
            $select->where(LabelInterface::STORE_ID . ' = ?', $storeId);
        }

        $select->where(LabelInterface::PROFILE_ID . ' = ?', $profileId);

        return $connection->fetchAll($select);
    }
}
