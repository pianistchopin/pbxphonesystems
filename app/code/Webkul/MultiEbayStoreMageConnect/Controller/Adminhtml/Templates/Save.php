<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Templates;

use Webkul\MultiEbayStoreMageConnect\Model\ListingTemplateFactory;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Templates;

class Save extends Templates
{
    /**
     * @param \Magento\Backend\App\Action\Context $context,
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper,
     * @param ListingTemplateFactory $listingTemplateFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        ListingTemplateFactory $listingTemplateFactory
    ) {
        $this->listingTemplateFactory = $listingTemplateFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            if (!$data) {
                $this->_redirect('multiebaystoremageconnect/templates/index');
                return;
            }
            $listingTemplate = $this->listingTemplateFactory->create();
            $listingTemplate->setData($data);
            if (isset($data['id'])) {
                $listingTemplate->setEntityId($data['id']);
            }
            $proAttr = isset($data['product_attr']) ? $data['product_attr'] : [];
            $listingTemplate->setMappedAttribute($this->jsonHelper->jsonEncode($proAttr));
            $listingTemplate->save();
            $this->messageManager->addSuccess(__('Listing template has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('multiebaystoremageconnect/templates/index');
    }
}
