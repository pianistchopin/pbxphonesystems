<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Testimonial
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Testimonial\Block\Adminhtml\Testimonial\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('testimonial_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Testimonial Information'));

        $this->addTab(
                'main_section',
                [
                    'label' => __('General Infomration'),
                    'content' => $this->getLayout()->createBlock('Ves\Testimonial\Block\Adminhtml\Testimonial\Edit\Tab\Main')->toHtml()
                ]
            );
         $this->addTab(
                'author_section',
                [
                    'label' => __('Author Information'),
                    'content' => $this->getLayout()->createBlock('Ves\Testimonial\Block\Adminhtml\Testimonial\Edit\Tab\Author')->toHtml()
                ]
            );
    }
}
