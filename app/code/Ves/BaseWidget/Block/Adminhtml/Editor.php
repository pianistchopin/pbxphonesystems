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

class Editor extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{ 
    protected $_value = null;
    protected $_model = null;

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
  /**
     * @param \Magento\Backend\Block\Template\Context                $context
     * @param \Ves\BaseWidget\Helper\Data                            $dataHelper        
     * @param \Magento\Framework\Registry                            $registry          
     * @param \Magento\Backend\Helper\Data                           $backendData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Ves\BaseWidget\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
        ){
        $this->_dataHelper = $dataHelper;
        $this->_coreRegistry = $registry;

        $this->_value = isset($data['value'])?$data['value']:'';

        $this->_model = isset($data['model'])?$data['model']:'';
        
        if(isset($data['template']) && $data['template']) {
          $this->setTemplate($data['template']);
        } elseif($this->hasData("template")) {
          $this->setTemplate($this->getData("template"));
        } else{
          $this->setTemplate("Ves_BaseWidget::builder/editor.phtml");
        }

        parent::__construct($context, $data);
    }

    
    protected function _toHtml() {
        $placeholder = "";
        //Get current layout profile params
        $params = $this->getBlock()->getParams();

        if(1 ==  $this->isProductBuilder() && empty($params)) { //get default layout when create new product profile
            $params = $this->_dataHelper->getDefaultProductLayout();
        }
        $this->assign("params", $params);

        return parent::_toHtml();
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

    public function getConfig($path, $section = "vespagebuilder") {
      return $this->_dataHelper->getConfig($path, null, "vespagebuilder");
    }

}
