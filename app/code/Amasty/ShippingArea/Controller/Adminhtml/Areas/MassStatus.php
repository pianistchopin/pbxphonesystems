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
use Magento\Ui\Component\MassAction\Filter;

class MassStatus extends Areas
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
        $statusValue = $this->getRequest()->getParam('status');
        $recordUpdates = 0;

        try {
            /** @var \Amasty\ShippingArea\Api\Data\AreaInterface $record */
            foreach ($collection->getItems() as $record) {
                $record->setIsEnabled($statusValue);
                $this->areaResource->save($record);
                $recordUpdates++;
            }
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Can\'t update some items. Please review the log and try again.')
            );
        }

        if ($recordUpdates) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $recordUpdates));
        }

        return $this->_redirect('*/*/');
    }
}
