<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Templates;

use Magento\Framework\Locale\Resolver;
use Webkul\MultiEbayStoreMageConnect\Model\ListingTemplateFactory;
use Magento\Framework\Registry;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Templates;

class Edit extends Templates
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\ListingTemplateFactory
     */
    private $listingTemplateFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context,
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory,
     * @param ListingTemplateFactory $listingTemplateFactory,
     * @param Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        ListingTemplateFactory $listingTemplateFactory,
        Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->listingTemplateFactory = $listingTemplateFactory;
        $this->coreRegistry = $registry;
        parent::__construct($context);
    }

   /**
    * Init actions
    *
    * @return \Magento\Backend\Model\View\Result\Page
    */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_MultiEbayStoreMageConnect::listing_templates')
            ->addBreadcrumb(__('Lists'), __('Lists'))
            ->addBreadcrumb(__('Manage Listing Template'), __('Manage Listing Template'));
        return $resultPage;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $listingTemplatesModel = $this->listingTemplateFactory->create();
        if ($id) {
            $listingTemplatesModel->load($id);
            if (!$listingTemplatesModel->getEntityId()) {
                $this->messageManager->addError(__('This listing template no longer exists.'));
                $this->_redirect('MultiEbayStoreMageConnect/*/');
                return;
            }
        }

        $this->coreRegistry->register('listing_templates', $listingTemplatesModel);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $label = $id ? __('Edit Listing Template') : __('New Listing Template');
        $resultPage->addBreadcrumb($label, $label);
        $resultPage->getConfig()->getTitle()->prepend($id ?__('Edit Listing Template') : __('New Listing Template'));
        return $resultPage;
    }
}
