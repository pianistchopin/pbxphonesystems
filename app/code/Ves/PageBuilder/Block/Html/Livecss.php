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
namespace Ves\PageBuilder\Block\Html;

use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Url;

class Livecss extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_page;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $_blockModel;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_pageId;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filesystem;
    protected $_readFactory;
    /** @var UrlBuilder */
    protected $actionUrlBuilder;
    protected $_authSession;
    protected $_default_css_folder;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Cms\Model\Page $page
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param array $data
     */


    /**
     * @param \Magento\Framework\View\Element\Template\Context                   $context          
     * @param \Ves\Themesettings\Model\System\Config\Source\Css\Font\GoogleFonts $_googleFontModel 
     * @param \Ves\Themesettings\Helper\Theme                                    $ves              
     * @param array                                                              $data             
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Page $page,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        /*\Magento\Store\Model\StoreManagerInterface $storeManager,*/
        \Magento\Cms\Model\PageFactory $pageFactory,
        /*\Magento\Framework\View\Page\Config $pageConfig,*/
        \Ves\PageBuilder\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        /*\Magento\Framework\Filesystem $filesystem,*/
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        ReadFactory $readFactory,
        Url $actionUrlBuilder,
        array $data = []
        ){
        parent::__construct($context, $data);

        $this->_dataHelper = $dataHelper;
        $this->_page = $page;
        $this->_filterProvider = $filterProvider;
        /*$this->_storeManager = $storeManager;*/
        $this->_pageFactory = $pageFactory;
        /*$this->pageConfig = $pageConfig;*/
        $this->_coreRegistry = $registry;
        /*$this->_filesystem = $filesystem;*/
        $this->_readFactory = $readFactory;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->_objectManager = $objectManager;
        $this->_authSession = $authSession;

    }

    public function _toHtml() {
        if(!$this->_dataHelper->getConfig('general/show', null, 0, 'veslivecss')) return;
        return parent::_toHtml();
    }

    public function getBlockHelper() {
        return $this->_dataHelper;
    }
    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }

    public function getPubDirPath( $path_type = "") {
        $path_type = $path_type?$path_type:DirectoryList::PUB;
        return $this->_filesystem->getDirectoryRead($path_type)->getAbsolutePath();
    }
    

    public function getCssProfiles() {
        $output = array();

        $path = $this->getPubDirPath() .'pagebuilder'.DIRECTORY_SEPARATOR.'pattern'.DIRECTORY_SEPARATOR;
        if($pattern_folder_path) {
            $pattern_folder_path = $this->getPubDirPath().$pattern_folder_path.DIRECTORY_SEPARATOR;
            if(is_dir($pattern_folder_path)) {
                $path = $pattern_folder_path;
            }
        }
        if( $path && is_dir($path) ) {
            $files = glob( $path.'*' );
            foreach( $files as $dir ){
                if( preg_match("#.png|.jpg|.gif#", $dir)){
                    $output[] = str_replace("","",basename( $dir ) );
                }
            }
        }
        return $output;
    }
    public function getCustomizeFolderURL( $custom_css_folder_path = "" ) {
        if($custom_css_folder_path) {
            $custom_css_folder_path = str_replace(DIRECTORY_SEPARATOR, "/", $custom_css_folder_path);
        } else{
            $custom_css_folder_path = $this->_default_css_folder;
        }
        

        return $this->getBaseUrl()."pub/".$custom_css_folder_path."/";
    }
    public function getLiveProfiles() {

    }
    public function getLiveEditLink() {
        return $this->actionUrlBuilder->getDirectUrl( "vespagebuilder/livecss/generate" );
    }
    /**
     *
     */
    public function renderEditorThemeForm( $profile_name = ""){
        if(!$profile_name) {
            $profile_name = $this->getBlockHelper()->getConfig("general/live_profile", null, "default", "veslivecss");
        }
        
        $path = $this->getPubDirPath() .'pagebuilder'.DIRECTORY_SEPARATOR.'livecss'.DIRECTORY_SEPARATOR.'profiles'.DIRECTORY_SEPARATOR.$profile_name.".xml";
        if(!file_exists($path)) {
            $path = $this->getPubDirPath() .'pagebuilder'.DIRECTORY_SEPARATOR.'livecss'.DIRECTORY_SEPARATOR.'profiles'.DIRECTORY_SEPARATOR."default.xml";
        }

        $output = array( 'selectors' => array(), 'elements' => array() );
        if( file_exists($path) ){
            $info = simplexml_load_file( $path );
            if( isset($info->selectors->items) ){
                foreach( $info->selectors->items as $item ){
                    $vars = get_object_vars($item);
                    if( is_object($vars['item']) ){
                        $tmp = get_object_vars( $vars['item'] );
                        $vars['selector'][] = $tmp;
                    }else {
                        foreach( $vars['item'] as $selector ){
                            $tmp = get_object_vars( $selector );
                            if( is_array($tmp) && !empty($tmp) ){
                                $vars['selector'][] = $tmp;
                            }
                        }
                    }
                    unset( $vars['item'] );
                    $output['selectors'][$vars['match']] = $vars;
                }
            }

            if( isset($info->elements->items) ){
                foreach( $info->elements->items as $item ){
                    $vars = get_object_vars($item);
                    if( is_object($vars['item']) ){
                        $tmp = get_object_vars( $vars['item'] );
                        $vars['selector'][] = $tmp;
                    }else {
                        foreach( $vars['item'] as $selector ){
                            $tmp = get_object_vars( $selector );
                            if( is_array($tmp) && !empty($tmp) ){
                                $vars['selector'][] = $tmp;
                            }
                        }
                    }
                    unset( $vars['item'] );
                    $output['elements'][$vars['match']] = $vars;
                }
            }
        }

        return $output;
    }

    public function getCustomizePath( $custom_css_folder_path = ""){
        $path = $this->getPubDirPath() .'pagebuilder'.DIRECTORY_SEPARATOR.'livecss'.DIRECTORY_SEPARATOR.'customize'.DIRECTORY_SEPARATOR;

        $this->_default_css_folder = "pagebuilder/livecss/customize";

        if($custom_css_folder_path) {
            $custom_css_folder_path_dir = $this->getPubDirPath().$custom_css_folder_path.DIRECTORY_SEPARATOR;
            if(is_dir($custom_css_folder_path_dir)) {
                $path = $custom_css_folder_path_dir;
                $this->_default_css_folder = $custom_css_folder_path;
            }
        }

        return $path;
    }

    /**
     *
     */
    public function getFileList( $path , $e=null, $filter_pattern = "" ) {
        $output = array();
        $directories = glob( $path.'*'.$e );
        if($directories) {
            foreach( $directories as $dir ){
                if($filter_pattern) {
                    $file_name = basename( $dir );
                    if(strpos($file_name, $filter_pattern) !== false) {
                        $output[] = basename( $dir );
                    }
                    
                } else {
                    $output[] = basename( $dir );
                }
                
            }  
        }
                 
        
        return $output;
    }

    /**
     *
     */
    public function getPattern( $pattern_folder_path = "" ){
        $output = array();

        $path = $this->getPubDirPath() .'pagebuilder'.DIRECTORY_SEPARATOR.'livecss'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'patterns'.DIRECTORY_SEPARATOR;
        if($pattern_folder_path) {
            $pattern_folder_path = $this->getPubDirPath().$pattern_folder_path.DIRECTORY_SEPARATOR;
            if(is_dir($pattern_folder_path)) {
                $path = $pattern_folder_path;
            }
        }
        if( $path && is_dir($path) ) {
            $files = glob( $path.'*' );
            foreach( $files as $dir ){
                if( preg_match("#.png|.jpg|.gif#", $dir)){
                    $output[] = str_replace("","",basename( $dir ) );
                }
            }
        }
        return $output;
    }
    public function generatePageStyles() {

        $css_code = "";

        return $css_code;
    }

    public function isAllowCurrentIp() {
        $allowedIPsString = $this->getBlockHelper()->getConfig("general/allowedIPs", null, "", "veslivecss");
        $allowFrontendForAdmins = $this->getBlockHelper()->getConfig("general/allowFrontendForAdmins", null, "1", "veslivecss");

        $adminIp = null;

        if (1 == $allowFrontendForAdmins && $this->_isAdminLoggedIn()) {
            
            return true;
            
        }
        if(!$allowedIPsString) {
            return true;
        }
        // remove spaces from string
        $allowedIPsString = preg_replace('/ /', '', $allowedIPsString);

        $allowedIPs = array();

        if ('' !== trim($allowedIPsString)) {
            $allowedIPs = explode(',', $allowedIPsString);
        }

        $currentIP = $_SERVER['REMOTE_ADDR'];
        
        if(empty($allowedIPs) || in_array($currentIP, $allowedIPs)) {
            // current user allowed to access website?
            return true;
        }

        return false;
    }
    /**
     * Retrieve admin session model
     *
     * @return AuthSession|Session|mixed|null
     */
    protected function _getSession()
    {
        if ($this->_authSession === null) {
            $this->_authSession = $this->_objectManager->get('Magento\Backend\Model\Auth\Session');
        }
        return $this->_authSession;
    }
    protected function _isAdminLoggedIn() {
        $admin_session = $this->_getSession();
        if($admin_session) {
            if($admin_user = $admin_session->getUser()) {
                $admin_user_id = $admin_user->getUserId();
                if(0 < (int)$admin_user_id) {
                    return true;
                }
            }
        } else {
            if(isset($_SESSION['logged_admin']) && $_SESSION['logged_admin']) {
                return (isset($_SESSION['logged_admin'][0]) && session_id($_SESSION['logged_admin'][0]))?true:false;
            }
        }
        return false;
    }
}