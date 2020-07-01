<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\PriceRules;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Registry;
use Webkul\MultiEbayStoreMageConnect\Model\PriceRuleFactory;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\PriceRules;

class Edit extends PriceRules
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\PriceRuleFactory
     */
    private $priceRuleFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Registry $registry,
        PriceRuleFactory $priceRuleFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->priceRuleFactory = $priceRuleFactory;
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
        $resultPage->setActiveMenu('Webkul_MultiEbayStoreMageConnect::products_pricerules')
            ->addBreadcrumb(__('Lists'), __('Lists'))
            ->addBreadcrumb(__('Price Rules'), __('Price Rules'));
        return $resultPage;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $eBayPriceRuleModel=$this->priceRuleFactory->create();
        if ($id) {
            $eBayPriceRuleModel->load($id);
            if (!$eBayPriceRuleModel->getEntityId()) {
                $this->messageManager->addError(__('This eBay price rule no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->coreRegistry->register('ebay_pricerule', $eBayPriceRuleModel);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Price Rules') : __('New Price Rules'),
            $id ? __('Edit Price Rules') : __('New Price Rules')
        );
        $resultPage->getConfig()->getTitle()->prepend($id ?__('Edit Price Rule') : __('New Price Rule'));

        return $resultPage;
    }
}
