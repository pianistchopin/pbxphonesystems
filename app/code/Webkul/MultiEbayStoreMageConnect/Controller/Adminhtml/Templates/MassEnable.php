<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Templates;

use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Templates;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\ListingTemplate\CollectionFactory;

class MassEnable extends Templates
{
    /**
     * Massactions filter.
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $templateCount = 0;
        foreach ($collection->getItems() as $template) {
            $template->setStatus(1)->setEntityId($template->getEntityId())->save();
            ++$templateCount;
        }
        $this->messageManager->addSuccess(__("A total of %1 record(s) have been enabled.", $templateCount));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
