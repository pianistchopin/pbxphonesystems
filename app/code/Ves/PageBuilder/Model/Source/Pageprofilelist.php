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
 * @package    Ves_PageBuilder
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\PageBuilder\Model\Source;

class Pageprofilelist implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Ves\PageBuilder\Model\Block
     */
    protected  $_block_model;
    
    /**
     * 
     * @param \Ves\PageBuilder\Model\Block $group
     */
    public function __construct(
        \Ves\PageBuilder\Model\Block $block_model
        ) {
        $this->_block_model = $block_model;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {	
        $blocks = $this->_block_model->getCollection()
        				->addFieldToFilter('status', '1')
        				->addFieldToFilter("block_type", "page");

        $blocksList = array();

        $blocksList = [
        				["value"=>"0", "label"=> __("-- Select A Profile --")]
        			  ];

        foreach ($blocks as $block) {
            $blocksList[] = array('label' => $block->getTitle(),
                'value' => $block->getId());
        }
        return $blocksList;
    }
}
