<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Block\Adminhtml\Product\Edit;

class Icons  extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Stockstatus\Helper\Image
     */
    public $_imageHelper;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    protected $_optionsCollection;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    protected $_attributeObject;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Stockstatus\Helper\Image $helper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
        $this->_imageHelper = $helper;
        $this->_jsonEncoder = $jsonEncoder;

        $this->_attributeObject = $this->_coreRegistry->registry('entity_attribute');
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Retrieve option values collection
     * It is represented by an array in case of system attribute
     *
     * @return array|\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    public function getOptionValuesCollection()
    {
        if (!$this->_optionsCollection) {
            $attribute = $this->attributeRepository->get(
                \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
                'custom_stock_status'
            );
            $options =  $attribute->getSource()->getAllOptions(true, true);
            $attribute->setData('options', $options);
            $this->_optionsCollection = $attribute->getOptions();
        }

        return $this->_optionsCollection;
    }

    public function getIcon($optionId)
    {
        return $this->_imageHelper->getStatusIconUrl($optionId);
    }
}
