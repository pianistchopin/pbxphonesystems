<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Controller\Adminhtml\Synonym;

use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Amasty\ElasticSearch\Api\Data\SynonymInterface;

/**
 * Class Delete
 */
class Delete extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ElasticSearch::synonym';

    /**
     * @var \Amasty\ElasticSearch\Model\SynonymRepository
     */
    private $synonymRepository;

    /**
     * @var \Amasty\ElasticSearch\Model\ResourceModel\Synonym\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    private $indexerRegistry;

    public function __construct(
        Action\Context $context,
        \Amasty\ElasticSearch\Model\SynonymRepository $synonymRepository,
        \Amasty\ElasticSearch\Model\ResourceModel\Synonym\CollectionFactory $collectionFactory,
        Filter $filter,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        parent::__construct($context);

        $this->synonymRepository = $synonymRepository;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $idsToRemove = [];

        $ids = $this->getRequest()->getParam(SynonymInterface::SYNONYM_ID);
        if ($ids) {
            $idsToRemove = [$ids];
        }

        if ($this->getRequest()->getParam(Filter::SELECTED_PARAM)
            || $this->getRequest()->getParam(Filter::EXCLUDED_PARAM)
        ) {
            $idsToRemove = $this->filter->getCollection($this->collectionFactory->create())->getAllIds();
        }
        if ($idsToRemove) {
            foreach ($idsToRemove as $id) {
                try {
                    $this->synonymRepository->deleteById($id);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }

            $this->messageManager->addSuccessMessage(
                __('%1 synonym(s) was successfully removed', count($idsToRemove))
            );
            $this->indexerRegistry->get(\Magento\CatalogSearch\Model\Indexer\Fulltext::INDEXER_ID)->invalidate();
        } else {
            $this->messageManager->addErrorMessage(__('Please select Synonym(s)'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
