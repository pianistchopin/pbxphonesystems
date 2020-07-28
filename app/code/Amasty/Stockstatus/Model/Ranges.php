<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Model;

class Ranges extends \Magento\Framework\Model\AbstractModel
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        $this->_init('Amasty\Stockstatus\Model\ResourceModel\Ranges');
    }

    public function loadByQty($qty)
    {
        $this->_getResource()->loadByQty($this, $qty);
    }

    /**
     * @param int $qty
     * @param array $rules
     */
    public function loadByQtyAndRule($qty, $rules)
    {
        $this->_getResource()->loadByQtyAndRule($this, $qty, $rules);
    }

    public function clear()
    {
        $this->_getResource()->deleteAll();
    }
}
