<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Controller\Adminhtml\Profile;

use Amasty\ShippingBar\Controller\Adminhtml\AbstractProfile;
use Amasty\ShippingBar\Model\ResourceModel\Profile as ProfileResource;
use Amasty\ShippingBar\Model\ResourceModel\Profile\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends AbstractProfile
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var ProfileResource
     */
    private $resource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        ProfileResource $resource,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $recordDeleted = 0;

        try {
            /** @var \Amasty\ShippingBar\Model\Profile $record */
            foreach ($collection->getItems() as $record) {
                $this->resource->delete($record);
                $recordDeleted++;
            }
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Can\'t delete some items. Please review the log and try again.')
            );
        }

        if ($recordDeleted) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $recordDeleted));
        }

        return $this->_redirect('*/*/');
    }
}
