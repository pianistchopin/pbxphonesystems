<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Block;

/**
 * Class RelatedSearchTerms
 */
class RelatedSearchTerms extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Xsearch\Model\ResourceModel\Query\Collection
     */
    private $queryCollection;

    /**
     * @var \Magento\Search\Model\Query
     */
    private $query;

    /**
     * @var \Amasty\Xsearch\Model\Config
     */
    private $config;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Xsearch\Model\ResourceModel\Query\CollectionFactory $collectionFactory,
        \Magento\Search\Model\QueryFactory $queryFactory,
        \Amasty\Xsearch\Model\Config $config,
        array $data = []
    ) {
        $this->queryCollection = $collectionFactory->create();
        $this->query = $queryFactory->get();
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getItems()
    {
        return $this->queryCollection->getRelatedTerms($this->query->getId());
    }

    /**
     * @return bool
     */
    public function isShowResultsCount()
    {
        return $this->config->canShowRelatedNumberResults();
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        return $this->config->canShowRelatedTerms($this->query->getNumResults());
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if (!$this->canShow()) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @param string $queryText
     * @return string
     */
    public function getLink($queryText)
    {
        return $this->getUrl('*/*/') . '?q=' . urlencode($queryText);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __('Related search terms');
    }
}
