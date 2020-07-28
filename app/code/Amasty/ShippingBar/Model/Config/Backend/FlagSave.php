<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Model\Config\Backend;

class FlagSave extends \Magento\Framework\App\Config\Value
{
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->cacheTypeList->invalidate(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
        }

        return parent::afterSave();
    }
}
