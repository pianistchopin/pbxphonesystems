<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\PriceRules;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\PriceRule\CollectionFactory;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\PriceRules;

class MassDelete extends PriceRules
{
    /**
     * OrdermapRepositoryInterface
     */
    private $orderMapRepository;

    /**
     * @param Context                     $context
     * @param OrdermapRepositoryInterface $orderMapRepository
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
        $ruleDeleted = 0;
        foreach ($collection->getItems() as $record) {
            $record->delete();
            ++$ruleDeleted;
        }
        $this->messageManager->addSuccess(__("A total of %1 record(s) have been deleted.", $ruleDeleted));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
