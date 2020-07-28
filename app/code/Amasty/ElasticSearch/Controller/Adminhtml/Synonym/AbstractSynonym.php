<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Controller\Adminhtml\Synonym;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;

/**
 * Class AbstractSynonym
 */
abstract class AbstractSynonym extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ElasticSearch::synonym';

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Amasty\ElasticSearch\Model\SynonymRepository
     */
    protected $synonymRepository;

    /**
     * @var \Amasty\ElasticSearch\Model\SynonymFactory
     */
    protected $synonymFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    public function __construct(
        Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Amasty\ElasticSearch\Model\SynonymRepository $synonymRepository,
        \Amasty\ElasticSearch\Model\SynonymFactory $synonymFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->synonymRepository = $synonymRepository;
        $this->synonymFactory = $synonymFactory;
        $this->registry = $registry;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Amasty_ElasticSearch::Amasty_ElasticSearch');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Synonyms'));

        return $resultPage;
    }
}
