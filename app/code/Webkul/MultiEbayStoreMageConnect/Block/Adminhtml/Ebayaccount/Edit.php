<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Initialize MultiEbayStoreMageConnect Ebayaccount edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Webkul_MultiEbayStoreMageConnect';
        $this->_controller = 'adminhtml_Ebayaccount';
        parent::_construct();
        if ($this->_isAllowedAction('Webkul_MultiEbayStoreMageConnect::ebay_account_connect')) {
            $this->buttonList->update('save', 'label', __('Save eBay Account'));
        } else {
            $this->buttonList->remove('save');
        }
        $this->buttonList->remove('reset');
    }

    /**
     * Retrieve text for header element depending on loaded Group
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('ebayaccount_info')->getId()) {
            return __("Edit eBay Account ");
        } else {
            return __('New eBay Account');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     *
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
