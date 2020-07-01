<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Templates\Edit;

class AttributeMap extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Webkul_MultiEbayStoreMageConnect::listing/template.phtml';

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\ListingTemplateFactory $listingTemplate
     */
    private $listingTemplate;

    /**
     * @param \Magento\Backend\Block\Template\Context $context,
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository,
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper,
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria,
     * @param \Magento\Framework\Registry $registry,
     * @param \Webkul\MultiEbayStoreMageConnect\Model\ListingTemplateFactory $listingTemplate,
     * @param \Webkul\MultiEbayStoreMageConnect\Logger\Logger $logger,
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria,
        \Magento\Framework\Registry $registry,
        \Webkul\MultiEbayStoreMageConnect\Model\ListingTemplateFactory $listingTemplate,
        \Webkul\MultiEbayStoreMageConnect\Logger\Logger $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productAttributeRepository = $productAttributeRepository;
        $this->jsonHelper = $jsonHelper;
        $this->searchCriteria = $searchCriteria;
        $this->coreRegistry = $registry;
        $this->listingTemplate = $listingTemplate;
        $this->logger = $logger;
    }

    /**
     * getMappedVariables
     */
    public function getMappedVariables()
    {
        try {
            $listingTemplates = $this->coreRegistry->registry('listing_templates');
            return $this->jsonHelper->jsonDecode($listingTemplates->getMappedAttribute());
        } catch (\Exception $e) {
            $this->logger->addError('getMappedVariables : '. $e->getMessage());
            return [];
        }
    }

    /**
     * getProductAttributeList
     * @return json
     */
    public function getProductAttributeList()
    {
        try {
            $searchCriteria = $this->searchCriteria->addFilter(
                'frontend_input',
                ['select', 'text', 'date', 'multiline', 'textarea', 'multiselect', 'price', 'weight', 'boolean'],
                'in'
            )->create();
            $attributeList = $this->productAttributeRepository->getList($searchCriteria)->getItems();
            return $attributeList;
        } catch (\Exception $e) {
            $this->logger->addError('getProductAttributeList : '. $e->getMessage());
            return [];
        }
    }
}
