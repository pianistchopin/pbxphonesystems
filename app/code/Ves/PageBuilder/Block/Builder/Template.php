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
namespace Ves\PageBuilder\Block\Builder;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Json\EncoderInterface;

class Template extends \Magento\Framework\View\Element\Template
{
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Ves\PageBuilder\Helper\Data
     */
    protected $_blockHelper;

    /**
    * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var string $_config
     * 
     * @access protected
     */
    protected $_listDesc = array();
    
    /**
     * @var string $_config
     * 
     * @access protected
     */
    protected $_show = 0;
    protected $_theme = "";

    protected $_banner = null;
    protected $jsonEncoder;

     /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    protected $_profile;

    protected $_settings;

    /**
     * Block Collection
     */
    protected $_blockCollection;

    protected $builderHelper;
   
	/**
     * @param \Magento\Framework\View\Element\Template\Context $context     
     * @param \Ves\PageBuilder\Helper\Data                    $_blockHelper 
     * @param array                                            $data        
     */
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ves\PageBuilder\Helper\Data $blockHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Ves\PageBuilder\Model\Block $blockCollection,
        \Ves\PageBuilder\Helper\Builder $builderHelper,
        EncoderInterface $jsonEncoder,
        array $data = []
        ) {
        $this->_blockHelper = $blockHelper;
        $this->_coreRegistry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->httpContext = $httpContext;
        $this->_blockCollection = $blockCollection;
        $this->builderHelper = $builderHelper;

        parent::__construct($context, $data);

        $my_template = "";
        if($this->hasData("template")) {
            $my_template = $this->getData("template");
        }
        if($my_template) {
            $this->setTemplate($my_template);
        }
    }

    public function setPageProfile($profile = null) {
        $this->_profile = $profile;
    }

    public function getDataFilter() {
        return $this->_blockHelper;
    }

    public function getJsonEncoder() {
        return $this->jsonEncoder;
    }

    public function setSettings($settings) {
        $this->_settings = $settings;
    }

    public function getSettings() {
        return $this->_settings;
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function _toHtml() {
        if($this->_profile) {
            $params = $this->_profile->getParams();
            $params = \Zend_Json::decode($params);

            $block_widgets = $this->_profile->getWidgets();

            $settings = $this->_profile->getSettings();
            $settings = unserialize($settings);
            $this->setSettings($settings);

            $this->assign("layouts", $params);
            $this->assign("block_widgets", $block_widgets);
            $this->assign("settings", $settings);
            $this->assign("is_container", $this->_profile->getContainer());
            $this->assign("class", $this->_profile->getPrefixClass());
            $this->assign("show_title", $this->getConfig("show_title"));
            $this->assign("disable_wrapper", $this->getConfig("disable_wrapper"));
            $this->assign("heading", $this->_profile->getTitle());

            if(1 == $this->_profile->getContainer()) {
                $this->setTemplate("builder/page_container.phtml");
            }
        }
        $settings = $this->_profile->getSettings();
        $html = "";
        if($settings && isset($settings['enable_wrapper']) && $settings['enable_wrapper'] == 1) {
            $wrapper_class = (isset($settings['select_wrapper_class'])?$settings['select_wrapper_class']." ":'');
            $wrapper_class .= (isset($settings['wrapper_class'])?$settings['wrapper_class']:'');

            if(isset($this->_profile) && $this->_profile) {
                $wrapper_class .= " ".$this->_profile->getAlias();
            }
            $html = '<div class="'.$wrapper_class.'">'.parent::_toHtml().'</div>';
        } else {
            $html = parent::_toHtml();
        }
        //Load custom assets files (css, js)
        if(isset($settings['custom_js']) && $settings['custom_js']) {
            $html .= '<script type="text/javascript">'.$settings['custom_js'].'</script>';
        }
        if(isset($settings['custom_css']) && $settings['custom_css']) {
            $html .= '<style type="text/css">'.$settings['custom_css'].'</style>';
        }
        
        return $html;

    }

    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getBaseMediaDir() {
        return $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
    }

    public function getImageUrl($image = "") {
        $base_media_url = $this->getBaseMediaUrl();
        $base_media_dir = $this->getBaseMediaDir();

        $_imageUrl = $base_media_dir.$image;

        if (file_exists($_imageUrl)){
            return $base_media_url.$image;
        }
        return false;
    }

    public function detectDeviceToShow($widget = array()){
        $hidden_class = "";
        $show_on_desktop = isset($widget['desktop'])?$widget['desktop']: true;
        $show_on_tablet = isset($widget['tablet'])?$widget['tablet']: true;
        $show_on_mobile = isset($widget['mobile'])?$widget['mobile']: true;

        if(!$show_on_desktop) {
            $hidden_class = "hidden-lg";
        }
        if(!$show_on_tablet) {
            $hidden_class = "hidden-sm";
        }
        if(!$show_on_mobile) {
            $hidden_class = "hidden-xs";
        }
        return $hidden_class;
    }

    public function getConfig($key, $default = '')
    {
        if($this->hasData($key))
        {
            return $this->getData($key);
        }
        return $default;
    }

    public function getRowStyle($row = array()) {
        $custom_css = array();
        
        if(isset($row['bgcolor']) && $row['bgcolor']) {
            $custom_css[] = 'background-color:'.$row['bgcolor'];
        }
        if(isset($row['bgimage']) && $row['bgimage']) {
            $row['bgimage'] = $this->getImageUrl( $row['bgimage'] );
            $custom_css[] = ($row['bgimage'])?'background-image:url('.$row['bgimage'].')':'';
        }
        if(isset($row['bgrepeat']) && $row['bgrepeat']) {
            $custom_css[] = 'background-repeat:'.$row['bgrepeat'];
        }
        if(isset($row['bgposition']) && $row['bgposition']) {
            $custom_css[] = 'background-position:'.$row['bgposition'];
        }
        if(isset($row['bgattachment']) && $row['bgattachment']) {
            $custom_css[] = 'background-attachment:'.$row['bgattachment'];
        }
        if(isset($row['padding']) && $row['padding']) {
            $custom_css[] = 'padding:'.$row['padding'];
        }
        if(isset($row['margin']) && $row['margin']) {
            $custom_css[] = 'margin:'.$row['margin'];
        }

        return $custom_css;
    }

    public function getRowInnerStyle($row = array()) {
        $custom_css = array();
        
        if(isset($row['inbgcolor']) && $row['inbgcolor']) {
            $custom_css[] = 'background-color:'.$row['inbgcolor'];
        }
        if(isset($row['inbgimage']) && $row['inbgimage']) {
            $row['inbgimage'] = $this->getImageUrl( $row['inbgimage'] );
            $custom_css[] = ($row['inbgimage'])?'background-image:url('.$row['inbgimage'].')':'';
        }
        if(isset($row['inbgrepeat']) && $row['inbgrepeat']) {
            $custom_css[] = 'background-repeat:'.$row['inbgrepeat'];
        }
        if(isset($row['inbgposition']) && $row['inbgposition']) {
            $custom_css[] = 'background-position:'.$row['inbgposition'];
        }
        if(isset($row['inbgattachment']) && $row['inbgattachment']) {
            $custom_css[] = 'background-attachment:'.$row['inbgattachment'];
        }

        return $custom_css;
    }

    public function getColStyle($col = array()) {
        $custom_col_css = array();

        if(isset($col['bgcolor']) && $col['bgcolor']) {
            $custom_col_css[] = 'background-color:'.$col['bgcolor'];
        }
        if(isset($col['bgimage']) && $col['bgimage']) {
            $col['bgimage'] = $this->getImageUrl( $col['bgimage'] );
            $custom_col_css[]= $col['bgimage']?'background-image:url('.$col['bgimage'].')':'';
        }
        if(isset($col['bgrepeat']) && $col['bgrepeat']) {
            $custom_col_css[] = 'background-repeat:'.$col['bgrepeat'];
        }
        if(isset($col['bgposition']) && $col['bgposition']) {
            $custom_col_css[] = 'background-position:'.$col['bgposition'];
        }
        if(isset($col['bgattachment']) && $col['bgattachment']) {
            $custom_col_css[] = 'background-attachment:'.$col['bgattachment'];
        }
        if(isset($col['padding']) && $col['padding']) {
            $custom_col_css[] = 'padding:'.$col['padding'];
        }
        if(isset($col['margin']) && $col['margin']) {
            $custom_col_css[] = 'margin:'.$col['margin'];
        }

        return $custom_col_css;
    }

    public function getWidgetStyle($col = array()) {
        $custom_widget_css = array();

        if(isset($col['bgcolor']) && $col['bgcolor']) {
            $custom_widget_css[] = 'background-color:'.$col['bgcolor'];
        }
        if(isset($col['bgimage']) && $col['bgimage']) {
            $col['bgimage'] = $this->getImageUrl( $col['bgimage'] );
            $custom_widget_css[]= $col['bgimage']?'background-image:url('.$col['bgimage'].')':'';
        }
        if(isset($col['bgrepeat']) && $col['bgrepeat']) {
            $custom_widget_css[] = 'background-repeat:'.$col['bgrepeat'];
        }
        if(isset($col['bgposition']) && $col['bgposition']) {
            $custom_widget_css[] = 'background-position:'.$col['bgposition'];
        }
        if(isset($col['bgattachment']) && $col['bgattachment']) {
            $custom_widget_css[] = 'background-attachment:'.$col['bgattachment'];
        }
        if(isset($col['padding']) && $col['padding']) {
            $custom_widget_css[] = 'padding:'.$col['padding'];
        }
        if(isset($col['margin']) && $col['margin']) {
            $custom_widget_css[] = 'margin:'.$col['margin'];
        }
        
        return $custom_widget_css;
    }

    public function generateElementWidget($shortcode = "") {
        $widget_array = $this->_blockCollection->parserShortcode($shortcode);
        if($widget_array && isset($widget_array['type'])) {
            switch ($widget_array['type']) {
                case 'Ves\PageBuilder\Block\Widget\Builder':
                    $block_id = isset($widget_array['block_id'])?$widget_array['block_id']:0;
                    $block_id = (int)$block_id;
                    if($block_id) {
                        $element_profile = $this->_blockCollection->load($block_id);
                        $settings = ['block_name' => 'builder_element_template',
                            'template' => 'builder/block_container.phtml'
                        ];
                        $template_content = $this->builderHelper->generateHtml($element_profile, $settings);
                        if($template_content) {
                            $shortcode = $template_content;
                        }
                    }
                    
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        return $shortcode;
    }
}