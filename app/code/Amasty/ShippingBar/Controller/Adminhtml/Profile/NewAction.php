<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Controller\Adminhtml\Profile;

use Amasty\ShippingBar\Controller\Adminhtml\AbstractProfile;

class NewAction extends AbstractProfile
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
