<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Block\Adminhtml;

/**
 * Class RelatedTerms
 */
class RelatedTerms extends \Magento\Backend\Block\Template
{
    /**
     * @var \Amasty\Xsearch\Block\Adminhtml\RelatedTerms\Grid
     */
    private $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonEncoder;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Serialize\Serializer\Json $jsonEncoder,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
        $this->setTemplate('Amasty_Xsearch::search/term/related.phtml');
    }

    /**
     * @return \Magento\Catalog\Block\Adminhtml\Category\Tab\Product|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Amasty\Xsearch\Block\Adminhtml\RelatedTerms\Grid::class,
                'search.terms.related.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getTermsJson()
    {
        $terms = $this->getTerm()->getRelatedTerms();
        if (!empty($terms)) {
            return $this->jsonEncoder->serialize($terms);
        }
        return '{}';
    }

    /**
     * Retrieve current category instance
     *
     * @return array|null
     */
    public function getTerm()
    {
        return $this->registry->registry('current_catalog_search');
    }
}
