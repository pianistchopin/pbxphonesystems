<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Model\ResourceModel\Ranges;

class Collection
    extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init('Amasty\Stockstatus\Model\Ranges',
            'Amasty\Stockstatus\Model\ResourceModel\Ranges'
        );
    }
}

  