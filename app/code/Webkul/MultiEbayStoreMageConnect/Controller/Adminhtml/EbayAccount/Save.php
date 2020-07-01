<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\EbayAccount;

use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\EbayAccount;

class Save extends EbayAccount
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var /Webkul\MultiEbayStoreMageConnect\Model/EbayaccountsFactory
     */
    protected $_ebayAccountsFactory;

    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Webkul\MultiEbayStoreMageConnect\Model\EbayaccountsFactory $ebayAccountsFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_ebayAccountsFactory = $ebayAccountsFactory;
    }

    /**
     * Ebay account details
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $flag = false;
        $reserveId = 0;
        $resultRedirect = $this->resultRedirectFactory->create();
        $parameters = $this->getRequest()->getParams();
        $ebayAccountsCollection = $this->_ebayAccountsFactory->create()->getCollection();
        $ebayAccountsCollection->addFieldToFilter('store_name', $parameters['store_name']);
        if ($ebayAccountsCollection->getSize()) {
            foreach ($ebayAccountsCollection as $record) {
                if ($record->getId()) {
                        $flag = true;
                        $reserveId = $record->getId();
                        break;
                }
            }
        }

        if (!empty($parameters)) {
            $model = $ebayAccountsCollection = $this->_ebayAccountsFactory->create();
            $id = (int) $this->getRequest()->getParam('id');
            $error = __('eBay user didn\'t authorize successfully, Please try again.');
            if ($reserveId > 0) {
                $params = ['id' => $id, '_current' => true];
                $this->messageManager
                    ->addSuccess(__('Ebay details saved successfully, Now edit the record for syncronization process'));
                if (isset($parameters['product_type_allowed']) && $parameters['product_type_allowed']) {
                    $parameters['product_type_allowed'] = implode(',', $parameters['product_type_allowed']);
                }
                $model->addData($parameters)->setId($id)->save();
            } else {
                $this->messageManager->addError($error);
                $this->_redirect('*/*/');
            }
        } else {
            $this->messageManager->addError(__('Something went wrong'));
            $this->_redirect('*/*/');
        }
        $this->_redirect('*/*/');
    }
}
