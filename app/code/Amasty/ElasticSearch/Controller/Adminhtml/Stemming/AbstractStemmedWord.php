<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Controller\Adminhtml\Stemming;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;

/**
 * Class AbstractStemmedWord
 */
abstract class AbstractStemmedWord extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ElasticSearch::stemming';

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Amasty\ElasticSearch\Model\StemmedWordRepository
     */
    protected $stemmedWordRepository;

    /**
     * @var \Amasty\ElasticSearch\Model\StemmedWordFactory
     */
    protected $stemmedWordFactory;

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
        \Amasty\ElasticSearch\Model\StemmedWordRepository $ruleRepository,
        \Amasty\ElasticSearch\Model\StemmedWordFactory $stemmedWordFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->stemmedWordRepository = $ruleRepository;
        $this->stemmedWordFactory = $stemmedWordFactory;
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
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Stemmed Words'));

        return $resultPage;
    }
}
