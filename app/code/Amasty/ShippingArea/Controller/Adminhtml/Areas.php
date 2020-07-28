<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Controller\Adminhtml;

abstract class Areas extends \Magento\Backend\App\Action
{
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_ShippingArea::shipping_area');
    }
}
