<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Model\ResourceModel;

use Amasty\ShippingBar\Api\Data\ProfileInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Profile extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'amasty_shipbar_profile';
    const DELIMITER = ',';

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _construct() //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $this->_init(self::TABLE_NAME, ProfileInterface::ID);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Amasty\ShippingBar\Model\Profile $object
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @throws LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $data = $object->getData();

        if (preg_match(
            '/<+\w+((\s+\w+(\s*=\s*(?:".*?"|\'.*?\'|[^\'">\s]+))?)+\s*|\s*)?\/?>/m',
            $object->getCustomStyle()
        )) {
            throw new LocalizedException(__('HTML tags not allowed in "Custom CSS" field.'));
        }

        foreach ($data as &$value) {
            if (is_array($value)) {
                $value = implode(self::DELIMITER, $value);
            }
        }

        $object->setData($data);

        return parent::_beforeSave($object);
    }
}
