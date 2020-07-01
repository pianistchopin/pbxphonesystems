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
 * @package    Ves_Brand
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\PageBuilder\Block\Adminhtml\Pagebuilder;

/**
 * Brand edit block
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize brand edit block
     *
     * @return void
     */
    protected function _construct(){
    	$this->_objectId = 'block_id';
    	$this->_blockGroup = 'Ves_PageBuilder';
    	$this->_controller = 'adminhtml_pagebuilder';

    	parent::_construct();

    	if($this->_isAllowedAction('Ves_PageBuilder::page_save')){
    		$this->buttonList->update('save','label',__('Save Profile'));
    		$this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );

            if($this->_coreRegistry->registry('ves_pagebuilder')->getId()) {
                $this->buttonList->add(
                    'duplicate',
                    [
                        'label' => __('Duplicate'),
                        'onclick' => 'duplicateItem()',
                        'data_attribute' => [
                            'mage-init' => [
                                'button' => ['event' => 'duplicateItem', 'target' => '#edit_form'],
                            ],
                        ]
                    ],
                    -200
                );
            }

    	}else{
    		$this->buttonList->remove('save');
    	}

    	if ($this->_isAllowedAction('Ves_PageBuilder::page_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Profile'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('ves_pagebuilder')->getId()) {
            return __("Edit Profile '%1'", $this->escapeHtml($this->_coreRegistry->registry('ves_pagebuilder')->getTitle()));
        } else {
            return __('New Profile');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('cms/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            require([
                        'jquery',
                    ], function($){
                        $('#edit_form').bind('beforeSubmit', function() {
                            triggerSaveForm();
                        });
                    })
            ";
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };

            function duplicateItem() {
                var form_action_url = jQuery('#edit_form').attr('action');
                form_action_url +='duplicate/1';
                jQuery('#edit_form').attr('action', form_action_url);
                jQuery('#edit_form').submit();
            }
        ";
        return parent::_prepareLayout();
    }
}
