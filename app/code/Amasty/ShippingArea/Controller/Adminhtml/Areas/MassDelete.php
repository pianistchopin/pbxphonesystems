<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Controller\Adminhtml\Areas;

use Amasty\ShippingArea\Controller\Adminhtml\Areas;
use Amasty\ShippingArea\Model\ResourceModel\Area;
use Amasty\ShippingArea\Model\ResourceModel\Area\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use phpDocumentor\Reflection\Types\This;

class MassDelete extends Areas
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var Area
     */
    private $areaResource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        Area $areaResource,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->areaResource = $areaResource;
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
            /** @var \Amasty\ShippingArea\Api\Data\AreaInterface $record */
            foreach ($collection->getItems() as $record) {
                $this->areaResource->delete($record);
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
