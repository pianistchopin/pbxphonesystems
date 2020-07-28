<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Controller\Adminhtml\Related;

use Magento\Framework\Registry;

class Grid extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    private $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    private $queryFactory;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Search\Model\QueryFactory $queryFactory,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->queryFactory = $queryFactory;
        $this->registry = $registry;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $term = $this->initTerm();
        if (!$term) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('search/*/', ['_current' => true, 'id' => null]);
        }
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                \Amasty\Xsearch\Block\Adminhtml\RelatedTerms\Grid::class,
                'search.terms.related.grid'
            )->toHtml()
        );
    }

    /**
     * @return bool|\Magento\Search\Model\Query
     */
    private function initTerm()
    {
        $termId = $this->getRequest()->getParam('id', false);

        if ($termId) {
            $term = $this->queryFactory->create()->load($termId);
            $this->registry->register('current_catalog_search', $term);
        } else {
            return false;
        }

        return $term;
    }
}
