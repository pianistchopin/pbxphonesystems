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

class Html extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
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

    protected $_storeManager;
    protected $_dataHelper;
    protected $_coreRegistry;
    protected $_widgetHelper;
	/**
     * @param \Magento\Backend\Block\Template\Context                $context           
     * @param \Magento\Framework\Data\Form\Element\Factory           $factoryElement    
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection 
     * @param Escaper                                                $escaper           
     * @param \Ves\BaseWidget\Helper\Data                            $dataHelper     
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
        \Magento\Backend\Helper\Data $backendData,
        array $data = []
        ){

        $this->_factoryElement = $factoryElement;
        $this->_factoryCollection = $factoryCollection;
        //$this->_layout = $layout;
        $this->_backendData = $backendData;
        $this->_dataHelper = $dataHelper;
        //$this->_storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        $this->_widgetHelper = $widgetHelper;
        parent::__construct($context, $data);
    }

    public function getStoreManager() {
        return $this->_storeManager;
    }
    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getBaseSecureMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA,['_secure' => true]);
    }

    public function getCurrentModuleUrl() {
        $base_url = $this->getStoreManager()->getStore()->getBaseUrl();
        $base_dir = $this->getWidgetHelper()->getRootDirPath();
        $base_dir = str_replace(DIRECTORY_SEPARATOR, "/", $base_dir);
        $module_view_path = $this->getWidgetHelper()->getViewDirPath();
        $module_view_path = str_replace($base_dir, "", $module_view_path);

        return "{$base_url}{$module_view_path}";
    }

    public function getWidgetHelper( ) {
      return $this->_widgetHelper;
    }
    public function getConnectorUrl($params = []) {
        return $this->getUrl(
            'vesbasewidget/basewidget/connector',
            $params
        );
    }

    public function getWidgetFormUrl($target_id = "") {
      $params = array();
      if($target_id) {
          $params['widget_target_id'] = $target_id;
      }
      return $this->getUrl(
            'adminhtml/widget/loadOptions',
            $params
        );
    }

    public function getListWidgetsUrl($target_id = "") {
      $params = array();
      if($target_id) {
          $params['widget_target_id'] = $target_id;
      }
      return $this->getUrl(
            'adminhtml/widget/index',
            $params
        );
    }

    public function getElementFormUrl($target_id = "") {
      $params = array();
      if($target_id) {
          $params['widget_target_id'] = $target_id;
      }
      return $this->getUrl(
            'vespagebuilder/blockbuilder/loadElement',
            $params
        );
    }

    public function getListElementsUrl($target_id = "") {
      $params = array();
      if($page = $this->getBlockData()) {
        $params['block_id'] = $this->getBlockData()->getId();
      }
      
      if($target_id) {
          $params['widget_target_id'] = $target_id;
      }
      return $this->getUrl(
            'vespagebuilder/blockbuilder/grid',
            $params
        );
    }

    public function getCMSBlockFormUrl($target_id = "") {
      $params = array();
      if($target_id) {
          $params['widget_target_id'] = $target_id;
      }
      return $this->getUrl(
            'vespagebuilder/blockbuilder/loadCMSBlock',
            $params
        );
    }

    public function getListCMSBlocksUrl($target_id = "") {
      $params = array();
      if($page = $this->getBlockData()) {
        $params['block_id'] = $this->getBlockData()->getId();
      }
      
      if($target_id) {
          $params['widget_target_id'] = $target_id;
      }
      return $this->getUrl(
            'vespagebuilder/blockbuilder/gridCMSBlock',
            $params
        );
    }

    public function getWidgetDataUrl($params = []) {
      if($page = $this->getBlockData()) {
        $params['block_id'] = $this->getBlockData()->getId();
      }
      return $this->getUrl(
            'vesbasewidget/basewidget/widgetdata',
            $params
        );
    }

    public function getImageUrl($secure = false) {
        if($secure) {
            return $this->getBaseSecureMediaUrl();
        } else {
            return $this->getBaseMediaUrl();
        }
        
    }
    public function getBlockData()
    {
        return $this->_coreRegistry->registry('ves_pagebuilder');
    }

    public function isPageBuilder() {
       $is_pagebuilder = $this->_coreRegistry->registry('is_pagebuilder');
       $is_blockbuilder = $this->_coreRegistry->registry('is_blockbuilder');
       return ($is_pagebuilder || $is_blockbuilder)?true:false;
    }
    public function getConfig($path, $section = "vespagebuilder") {
      return $this->_dataHelper->getConfig($path, null, "vespagebuilder");
    }

}