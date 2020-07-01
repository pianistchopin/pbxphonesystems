<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\EbayAccount;

use Magento\Framework\Controller\ResultFactory;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\EbayAccount;

class Edit extends EbayAccount
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\Ebayaccounts
     */
    protected $_ebayAccounts;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Webkul\ImageGallery\Model\GroupsFactory $groups
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Webkul\MultiEbayStoreMageConnect\Model\EbayaccountsFactory $ebayAccounts
    ) {
        $this->_backendSession = $context->getSession();
        $this->_registry = $registry;
        $this->_ebayAccounts = $ebayAccounts;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $ebayAccountModel = $this->_ebayAccounts->create();
        if ($this->getRequest()->getParam('id')) {
            $ebayAccountModel->load($this->getRequest()->getParam('id'));
        }
        $data = $this->_backendSession->getFormData(true);
        if (!empty($data)) {
            $ebayAccountModel->setData($data);
        }
        $this->_registry->register('ebayaccount_info', $ebayAccountModel);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Webkul_MultiEbayStoreMageConnect::manager');
        $resultPage->getConfig()->getTitle()->prepend(__('Ebay Account'));
        $resultPage->getConfig()->getTitle()->prepend(
            $ebayAccountModel->getId() ? $ebayAccountModel->getGroupCode() : __('Ebay Account')
        );

        $left = $resultPage->getLayout()
            ->createBlock('Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount\Edit\Tabs');
        $resultPage->addLeft($left);
        $content = $resultPage->getLayout()
            ->createBlock('Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount\Edit');
        $resultPage->addContent($content);
        return $resultPage;
    }
}
