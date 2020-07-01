<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_BaseWidget
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Model\Config\Source;
class ListRuleGroup implements \Magento\Framework\Option\ArrayInterface
{
    protected  $_groupModel;

    /**
     * @param \Magento\Cms\Model\Block $ruleModel
     */
    public function __construct(
        \Magento\SalesRule\Model\Rule $ruleModel
        ) {
        $this->_groupModel = $ruleModel;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_groupModel->getCollection();
        $blocks = [];
        $blocks[] = [
                'value' => '',
                'label' => __("--Select a Rule Group--")
                ];
        foreach ($collection as $_block) {
            $blocks[] = [
                'value' => $_block->getRuleId(),
                'label' => $_block->getName()
            ];
        }
        return $blocks;
    }
}