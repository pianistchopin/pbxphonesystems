<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Controller\Adminhtml\Group;

/**
 * Class Index
 * @package Amasty\Shopby\Controller\Adminhtml\Group
 */
class Index extends \Amasty\Shopby\Controller\Adminhtml\Group
{
    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Amasty_Shopby::group_attributes')
            ->addBreadcrumb(__('Manage Group Attribute Options'), __('Manage Group Attribute Options'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Group Attribute Options'));
        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Shopby::group_attributes');
    }
}
