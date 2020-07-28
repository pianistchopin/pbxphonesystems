<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Block\Adminhtml;

/**
 * Class Group
 * @package Amasty\Shopby\Block\Adminhtml
 */
class Group extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Amasty_Shopby';
        $this->_controller = 'adminhtml_group';
        $this->_headerText = __('Manage Group Attribute Options');
        $this->_addButtonLabel = __('Add New Group');
        parent::_construct();
    }
}
