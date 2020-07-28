<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Controller\Adminhtml\Stemming;

use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Amasty\ElasticSearch\Api\Data\StemmedWordInterface;

/**
 * Class Delete
 */
class Delete extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ElasticSearch::stemming';

    /**
     * @var \Amasty\ElasticSearch\Model\StemmedWordRepository
     */
    private $stemmedWordRepository;

    /**
     * @var \Amasty\ElasticSearch\Model\ResourceModel\StemmedWord\CollectionFactory
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
        \Amasty\ElasticSearch\Model\StemmedWordRepository $stemmedWordRepository,
        \Amasty\ElasticSearch\Model\ResourceModel\StemmedWord\CollectionFactory $collectionFactory,
        Filter $filter,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        parent::__construct($context);

        $this->stemmedWordRepository = $stemmedWordRepository;
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

        $ids = $this->getRequest()->getParam(StemmedWordInterface::STEMMED_WORD_ID);
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
                    $this->stemmedWordRepository->deleteById($id);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }

            $this->messageManager->addSuccessMessage(
                __('%1 stemmed word(s) was successfully removed', count($idsToRemove))
            );
        } else {
            $this->messageManager->addErrorMessage(__('Please select Stemmed Word(s)'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
