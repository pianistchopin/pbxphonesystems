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
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\MultiEbayStoreMageConnect\Model\ResourceModel\Ebayaccounts\CollectionFactory;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\EbayAccount;

class MassDelete extends EbayAccount
{
    /**
     * Massactions filter.
     *
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
    
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->_filter
                            ->getCollection(
                                $this->_collectionFactory->create()
                            );
        $storeRecordDeleted = 0;
        foreach ($collection->getItems() as $ebayStore) {
            $ebayStore->setId($ebayStore->getEntityId());
            $ebayStore->delete();
            ++$storeRecordDeleted;
        }
        $this->messageManager->addSuccess(
            __("A total of %1 record(s) have been deleted.", $storeRecordDeleted)
        );

        return $this->resultFactory
                    ->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
