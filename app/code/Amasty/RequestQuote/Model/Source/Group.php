<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Source;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Convert\DataObject as Converter;
use Magento\Framework\Option\ArrayInterface;

class Group implements ArrayInterface
{
    /**
     * @var array|null
     */
    private $options;

    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @var Converter
     */
    private $converter;

    public function __construct(
        Converter $converter,
        GroupManagementInterface $groupManagement
    ) {
        $this->groupManagement = $groupManagement;
        $this->converter = $converter;
    }

    /**
     * Retrieve customer groups as array
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->converter->toOptionArray(
                $this->groupManagement->getLoggedInGroups(),
                'id',
                'code'
            );
            $notLoggedGroup = $this->groupManagement->getNotLoggedInGroup();
            array_unshift($this->options, [
                'value' => $notLoggedGroup->getId(),
                'label' => __('Not Logged In')
            ]);
        }
        return $this->options;
    }
}
