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
 * @package    Ves_ImageSlider
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Block\Adminhtml;

class InitBuilder extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{ 
    protected $_value = null;
    protected $_model = null;
    /**
     * @var \Magento\Framework\Data\Form\Element\CollectionFactory
     */
    protected $_factoryCollection;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_factoryElement;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData = null;
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_dataHelper;
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_coreRegistry;
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_widgetHelper;
    /**
     * @var \Magento\Widget\Model\Widget
     */
    protected $_widgetModel;

    protected $_blockProfileModel;
  /**
     * @param \Magento\Backend\Block\Template\Context                $context           
     * @param \Magento\Framework\Data\Form\Element\Factory           $factoryElement    
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection 
     * @param Escaper                                                $escaper           
     * @param \Ves\BaseWidget\Helper\Data                            $dataHelper     
     * @param \Ves\BaseWidget\Helper\Widget                          $widgetHelper   
     * @param \Magento\Framework\View\LayoutInterface                $layout            
     * @param \Magento\Backend\Helper\Data                           $backendData       
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Ves\BaseWidget\Helper\Data $dataHelper,
        \Ves\BaseWidget\Helper\Widget $widgetHelper,
        //\Magento\Store\Model\StoreManagerInterface $storeManager,
        //\Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Registry $registry,
        \Magento\Widget\Model\Widget $widgetModel,
        \Magento\Backend\Helper\Data $backendData,
        \Ves\PageBuilder\Model\Source\Blockprofilelist $blockProfileModel,
        array $data = []
        ){
        $this->_factoryElement = $factoryElement;
        $this->_factoryCollection = $factoryCollection;
        //$this->_layout = $layout;
        $this->_widgetHelper = $widgetHelper;
        $this->_backendData = $backendData;
        $this->_dataHelper = $dataHelper;
        //$this->_storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        $this->_widgetModel = $widgetModel;
        $this->_blockProfileModel = $blockProfileModel;

        $this->_value = isset($data['value'])?$data['value']:'';

        $this->_model = isset($data['model'])?$data['model']:'';
        

        parent::__construct($context, $data);
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
    
    protected function _toHtml() {
        $placeholder = "";

        //Get current layout profile params
        $params = $this->getBlock()->getParams();

        if(1 ==  $this->isProductBuilder() && empty($params)) { //get default layout when create new product profile
            $params = $this->_dataHelper->getDefaultProductLayout();
        }

        //Get page type
        $page_type = "block";
        if(1 ==  $this->isProductBuilder()) {
          $page_type = "product";
        } elseif(1 == $this->isPageBuilder()){
          $page_type = "page";
        }

        
        $backup_params = array();
        
        if($this->_dataHelper->getConfig("general/auto_backup_profile", null, "vespagebuilder")) {
            $folder = "";
            if(1 ==  $this->isProductBuilder()){ //Load sample profile of product when we are managing product layout builder
              $folder = "vesproductbuilder";
            } elseif(1 == $this->isPageBuilder()){ //Load sample profile of page when we are managing page layout builder
              $folder = "vespagebuilder";
            } else {
               $folder = "vesblockbuilder";
            }
            $backup_params = $this->_dataHelper->getBackupLayouts( $folder );
        }

        //Get list sample layout profile params
        $sample_params = $this->_dataHelper->getSampleLayoutParams( $page_type );
        
        
        //Get available widgets in magento
        $avaialable_widgets = $this->_getAvailableWidgets();

        $widgets_info = $this->_widgetHelper->getListWidgetTypes("array", $avaialable_widgets);
        $widgets_json = $widgets_info?\Zend_Json::encode( $widgets_info ): "";
        $widgets_json = str_replace( array('\n','\r','\t') ,"", $widgets_json);

        $block_widgets = $this->getBlock()->getWidgets();
        $tmp_widgets = [];
        if($block_widgets) {
          foreach($block_widgets as $key=>$val) {
              $tmp = [];
              $tmp['wkey'] = $key;
              $tmp['shortcode'] = $val;
              $tmp_widgets[] = $tmp;
          }
        }

        $block_widgets_json = $tmp_widgets?\Zend_Json::encode( $tmp_widgets ): "";
        $block_widgets_json = str_replace( array('\n','\r','\t') ,"", $block_widgets_json);
        
        $this->assign("widgets_json", $widgets_json);
        $this->assign("widgets", $widgets_info);
        $this->assign("placeholder", $placeholder);
        $this->assign("value", $this->_value);
        $this->assign("builder_data", $this->_model);
        $this->assign("block_widgets_json", $block_widgets_json);
        $this->assign("params", $params);
        $this->assign("sample_params", $sample_params);
        $this->assign("backup_params", $backup_params);

        return parent::_toHtml();
    }
    /**
     * Return array of available widgets based on configuration
     *
     * @return array
     */
    protected function _getAvailableWidgets()
    {
        $result = array();
        $allWidgets = $this->_widgetModel->getWidgetsArray();

        $skipped = $this->_getSkippedWidgets();
        foreach ($allWidgets as $widget) {
            if (is_array($skipped) && in_array($widget['type'], $skipped)) {
                continue;
            }
            $result[] = $widget;
        }

        return $result;
    }
    protected function _getSkippedWidgets() {
        return null;
    }

    protected function getBlock()
    {
        if($this->_model) {
          return $this->_model;
        } else {
          $this->_model = $this->_coreRegistry->registry('ves_pagebuilder');
          return $this->_model;
        }
      
    }

    protected function isProductBuilder()
    {
      return $this->_coreRegistry->registry('is_productbuilder');
    }

    protected function isPageBuilder()
    {
      return $this->_coreRegistry->registry('is_pagebuilder');
    }

   public function getRowClass() {
        return array(   "default",
                        "primary",
                        "success",
                        "info",
                        "warning",
                        "danger",
                        "highlighted",
                        "darked",
                        "nopadding",
                        "no-padding",
                        "no-margin"
                    );
    }
    public function getRowRepeats() {
        return array(   "" => __("Theme Default"),
                        "repeat" => __("Repeat"),
                        "repeat-x" => __("Repeat X"),
                        "repeat-y" => __("Repeat Y"),
                        "no-repeat" => __("No Repeat"),
                        "inherit" => __("Inherits from parent element")
                    );
    }
    public function getRowAttachments() {
        return array(   "" => __("Theme Default"),
                        "scroll" => __("The background scrolls along with the element"),
                        "fixed" => __("The background is fixed with regard to the viewport"),
                        "local" => __("The background scrolls along with the elements contents"),
                        "inherit" => __("Inherits from parent element")
                    );
    }
    public function getRowPositions() {
        return array(   "" => __("Theme Default"),
                        "left top" => __("left top"),
                        "left center" => __("left center"),
                        "left bottom" => __("left bottom"),
                        "right top" => __("right top"),
                        "right center" => __("right center"),
                        "right bottom" => __("right bottom"),
                        "center top" => __("center top"),
                        "center center" => __("center center"),
                        "center bottom"  => __("center bottom")
                    );
    }
    public function getCSSAnimations(){
        return array(
                  array('value' => "", 'label'=>__('No Animation')),
                  array('value' => "bounce", 'label'=>__('bounce')),
                  array('value' => "flash", 'label'=>__('flash')),
                  array('value' => "pulse", 'label'=>__('pulse')),
                  array('value' => "rubberBand", 'label'=>__('rubberBand')),
                  array('value' => "shake", 'label'=>__('shake')),
                  array('value' => "swing", 'label'=>__('swing')),
                  array('value' => "tada", 'label'=>__('tada')),
                  array('value' => "wobble", 'label'=>__('wobble')),
                  array('value' => "bounceIn", 'label'=>__('bounceIn')),
                  array('value' => "bounceInDown", 'label'=>__('bounceInDown')),
                  array('value' => "bounceInLeft", 'label'=>__('bounceInLeft')),
                  array('value' => "bounceInRight", 'label'=>__('bounceInRight')),
                  array('value' => "bounceInUp", 'label'=>__('bounceInUp')),
                  array('value' => "fadeIn", 'label'=>__('fadeIn')),
                  array('value' => "fadeInDown", 'label'=>__('fadeInDown')),
                  array('value' => "fadeInDownBig", 'label'=>__('fadeInDownBig')),
                  array('value' => "fadeInLeft", 'label'=>__('fadeInLeft')),
                  array('value' => "fadeInLeftBig", 'label'=>__('fadeInLeftBig')),
                  array('value' => "fadeInRight", 'label'=>__('fadeInRight')),
                  array('value' => "fadeInRightBig", 'label'=>__('fadeInRightBig')),
                  array('value' => "fadeInUp", 'label'=>__('fadeInUp')),
                  array('value' => "fadeInUpBig", 'label'=>__('fadeInUpBig')),
                  array('value' => "flip", 'label'=>__('flip')),
                  array('value' => "flipInX", 'label'=>__('flipInX')),
                  array('value' => "flipInY", 'label'=>__('flipInY')),
                  array('value' => "lightSpeedIn", 'label'=>__('lightSpeedIn')),
                  array('value' => "rotateIn", 'label'=>__('rotateIn')),
                  array('value' => "rotateInDownLeft", 'label'=>__('rotateInDownLeft')),
                  array('value' => "rotateInDownRight", 'label'=>__('rotateInDownRight')),
                  array('value' => "rotateInUpLeft", 'label'=>__('rotateInUpLeft')),
                  array('value' => "rotateInUpRight", 'label'=>__('rotateInUpRight')),
                  array('value' => "hinge", 'label'=>__('hinge')),
                  array('value' => "rollIn", 'label'=>__('rollIn')),
                  array('value' => "zoomIn", 'label'=>__('zoomIn')),
                  array('value' => "zoomInDown", 'label'=>__('zoomInDown')),
                  array('value' => "zoomInLeft", 'label'=>__('zoomInLeft')),
                  array('value' => "zoomInRight", 'label'=>__('zoomInRight')),
                  array('value' => "zoomInUp", 'label'=>__('zoomInUp'))
                  );
    }

    public function getWidgetClasses() {
        return array("" => __("Default"),
                    "primary" => __("Primary"),
                    "danger" => __("Danger"),
                    "info" => __("Info"),
                    "warning" => __("Warning"),
                    "highlighted" => __("Highlighted"),
                    "nopadding" => __("Nopadding"),
                    "darked" => __("Darked"),
                    "no-padding" => __("no-padding"),
                    "no-margin" => __("no-margin")
                    );
    }

    public function getOffCanvasTypes() {
        return array("" => __("Disable"),
                    "left" => __("Enable Left Sidebar"),
                    "right" => __("Enable Right Sidebar"),
                    "both" => __("Enable Both Left & Right Sidebar")
                    );
    }

    public function getOffColTypes() {
        return array("" => __("Default"),
                    "left" => __("Offcanvas Left"),
                    "right" => __("Offcanvas Right"),
                    "main-column" => __("Main Column")
                    );
    }

    public function getElementProfiles() {
        return $this->_blockProfileModel->toOptionArray();
    }

    public function getWidgetInfoUrl() {
      $params = array();
      return $this->getUrl(
            'vespagebuilder/blockbuilder/getWidgetInfo',
            $params
        );
    }
    public function getConfig($path, $section = "vespagebuilder") {
      return $this->_dataHelper->getConfig($path, null, "vespagebuilder");
    }
}
