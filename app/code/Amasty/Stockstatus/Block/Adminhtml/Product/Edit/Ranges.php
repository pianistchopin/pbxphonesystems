<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Block\Adminhtml\Product\Edit;

use Amasty\Stockstatus\Model\Source\BackOrder;
use Magento\Catalog\Model\Product\Attribute\OptionManagementFactory;

class Ranges  extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    private $ruleCollection;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    private $optionsCollection;

    /**
     * @var \Amasty\Stockstatus\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var OptionManagementFactory
     */
    private $optionManagementFactory;

    /**
     * @var \Amasty\Stockstatus\Model\RangesFactory
     */
    private $rangesFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Stockstatus\Helper\Data $helper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        OptionManagementFactory $optionManagementFactory,
        \Amasty\Stockstatus\Model\RangesFactory $rangesFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->jsonEncoder = $jsonEncoder;
        $this->optionManagementFactory = $optionManagementFactory;
        $this->rangesFactory = $rangesFactory;
    }

    /**
     * Retrieve option values collection
     * It is represented by an array in case of system attribute
     *
     * @return array|\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    private function getOptionValuesCollection()
    {
        if (!$this->optionsCollection) {
            $model = $this->optionManagementFactory->create();
            $this->optionsCollection = $model->getItems('custom_stock_status');
        }

        return $this->optionsCollection;
    }

    public function getOptionValuesJson()
    {
        $data = [];
        foreach ($this->getOptionValuesCollection() as $item) {
            $data[] = [
                'option_id' => $item['value'],
                'value' => $item['label']
            ];
        };

        return $this->jsonEncoder->encode($data);
    }

    public function getRuleValuesJson()
    {
        $data = [];
        foreach ($this->getRuleValuesCollection() as $item) {
            $data[] = [
                'option_id' => $item['value'],
                'value' => $item['label']
            ];
        }

        $data = array_merge($data, BackOrder::toArray());
        return $this->jsonEncoder->encode($data);
    }

    /**
     * @return array|\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    private function getRuleValuesCollection()
    {
        if (!$this->ruleCollection) {
            $model = $this->optionManagementFactory->create();
            $this->ruleCollection = $model->getItems('custom_stock_status_qty_rule');
        }

        return $this->ruleCollection;
    }

    /**
     * @return array|\Amasty\Stockstatus\Model\ResourceModel\Ranges\Collection
     */
    public function getRanges()
    {
        $collection = $this->rangesFactory->create()->getCollection();
        $collection->getSelect()->order('qty_from');

        return $collection;
    }

    /**
     * @return \Amasty\Stockstatus\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }
}
