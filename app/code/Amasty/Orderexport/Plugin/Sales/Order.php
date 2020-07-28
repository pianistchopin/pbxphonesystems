<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderexport
 */

namespace Amasty\Orderexport\Plugin\Sales;

use Magento\Framework\App\Area as Area;

class Order
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Amasty\Orderexport\Model\ResourceModel\Profiles\Collection
     */
    protected $_profilesCollection;

    /**
     * @var \Amasty\Orderexport\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_state;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Orderexport\Model\ResourceModel\Profiles\Collection $profilesCollection,
        \Amasty\Orderexport\Helper\Data $helper,
        \Magento\Framework\App\State $state
    ) {
        $this->_helper             = $helper;
        $this->_registry           = $registry;
        $this->_objectManager      = $objectManager;
        $this->_profilesCollection = $profilesCollection;
        $this->_state              = $state;
    }

    public function afterSave($subject, $value)
    {

        if (!$this->_helper->getModuleConfig('general/enabled')) {
            return $value;
        }

        if ($this->_registry->registry('amorderexport_manual_run_triggered')) {
            return $value;
        }

        if ($this->_registry->registry('amorderexport_auto_run_triggered')) {
            return $value;
        }

        $collection = $this->_profilesCollection->addFieldToFilter('run_after_order_creation', 1);

        foreach ($collection as $profile) {
            $profile->run(null);
        }

        return $value;
    }

}
