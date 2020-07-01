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
namespace Ves\PageBuilder\Block\Adminhtml\Blockbuilder\Edit\Tab;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Design extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\View\Design\Theme\LabelFactory
     */
    protected $_labelFactory;

    /**
     * @var \Magento\Theme\Model\Layout\Source\Layout
     */
    protected $_pageLayout;

    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * @var \Ves\PageBuilder\Helper\Data
     */
    protected $_blockHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context                          $context           
     * @param \Magento\Framework\Registry                                      $registry          
     * @param \Magento\Framework\Data\FormFactory                              $formFactory       
     * @param \Magento\Theme\Model\Layout\Source\Layout                        $pageLayout        
     * @param \Magento\Framework\View\Design\Theme\LabelFactory                $labelFactory      
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder 
     * @param array                                                            $data              
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Theme\Model\Layout\Source\Layout $pageLayout,
        \Magento\Framework\View\Design\Theme\LabelFactory $labelFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Ves\PageBuilder\Helper\Data $blockHelper,
        array $data = []
    ) {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->_labelFactory = $labelFactory;
        $this->_pageLayout = $pageLayout;
        $this->_blockHelper = $blockHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form tab configuration
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Initialise form fields
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $this->_eventManager->dispatch(
        'ves_check_license',
        ['obj' => $this,'ex'=>'Ves_PageBuilder']
        );
        /*
         * Checking if user have permissions to save information
         */
        $isElementDisabled = !$this->_isAllowedAction('Ves_PageBuilder::block_edit');

        if (($this->hasData('is_valid') && $this->hasData('local_valid')) && !$this->getData('is_valid') && !$this->getData('local_valid')) {
            $isElementDisabled = true;
        }
        
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(['data' => ['html_id_prefix' => 'block_']]);

        $model = $this->_coreRegistry->registry('ves_pagebuilder');

        $layoutFieldset = $form->addFieldset(
            'layout_fieldset',
            ['legend' => __('Design Elemment'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        );


        $layoutFieldset->addField(
            'prefix_class',
            'text',
            [
                'name' => 'prefix_class',
                'label' => __('Prefix Class'),
                'title' => __('Prefix Class'),
                'disabled' => $isElementDisabled
            ]
            );

        $layoutFieldset->addField(
            'container',
            'select',
            [
                'label' => __('Enable Container'),
                'title' => __('Enable Container'),
                'name' => 'container',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        if($this->_blockHelper->getConfig('general/auto_backup_profile')) {

            $options = array("" => __('-- Load A Layout --'));
            if($model->getParams()) {
                $options['default'] = __('Current Layout Profile');
            }
            
            $folder = "";
            $folder = "vesblockbuilder";

            $backup_layouts = $this->_blockHelper->getBackupLayouts( $folder );

            $this->_coreRegistry->register("backup_layouts", $backup_layouts);
           
            if($backup_layouts) {
                foreach($backup_layouts as $key=>$val) {
                    $key_label = $key;
                    $arr_key = explode("_bak_", $key);
                    if(count($arr_key) > 1) {
                        $tmp_key2 = str_replace("-json","", $arr_key[1]);
                        $tmp_key2 = date("Y-m-d H:i:s", $tmp_key2);
                        $key_label = $arr_key[0]." ".$tmp_key2."-json";
                    }
                    $options[$key] = $key_label;
                }
            }

            $layoutFieldset->addField(
                'load_sample_layout',
                'select',
                [
                    'label' => __('Use Backup Layout'),
                    'title' => __('Use Backup Layout'),
                    'name' => 'load_sample_layout',
                    'options' => $options,
                    'disabled' => $isElementDisabled
                ]
            );

        }

        $lastEvent = "";

        
        $layoutFieldset->addType('extended_editor','\Ves\PageBuilder\Helper\Form\Element\Extendededitor');

        $layoutFieldset->addField(
            'block_editor',
            'extended_editor',
            [
                'label' => __('Element Editor'),
                'title' => __('Element Editor'),
                'name' => 'block_editor',
                'block_id'      => 'wpo-widgetform',
                'model_data'    => $model,
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('adminhtml_cms_page_edit_tab_design_prepare_form', ['form' => $form]);
        
        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Design');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Design');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
}
