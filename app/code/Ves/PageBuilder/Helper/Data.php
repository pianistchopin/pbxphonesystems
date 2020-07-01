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
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\PageBuilder\Helper;

use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\UrlInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected static $_list_dragable_blocks = array("Ves_",
                                                    "Mage_Page_Block_Html_Header",
                                                    "Mage_Page_Block_Html_Breadcrumbs",
                                                    "Mage_Reports_Block_Product_Viewed",
                                                    "Mage_Catalog_Block_Layer_View",
                                                    "Mage_Tag_Block_Popular",
                                                    "Mage_Catalog_Block_Category_View",
                                                    "Mage_Catalog_Block_Product_List",
                                                    "Mage_Catalog_Block_Product_Compare_Sidebar",
                                                    "Mage_Poll_Block_ActivePoll",
                                                    "Mage_Paypal_Block_Logo",
                                                    "Mage_Tag_Block_Popular",
                                                    "Mage_Page_Block_Html_Footer"
                                                    );
    protected static $_list_editable_blocks = array("Ves_",
                                                    "Mage_Page_Block_Html_Header",
                                                    "Mage_Page_Block_Html_Breadcrumbs",
                                                    "Mage_Reports_Block_Product_Viewed",
                                                    "Mage_Catalog_Block_Layer_View",
                                                    "Mage_Tag_Block_Popular",
                                                    "Mage_Catalog_Block_Category_View",
                                                    "Mage_Catalog_Block_Product_List",
                                                    "Mage_Catalog_Block_Product_Compare_Sidebar",
                                                    "Mage_Poll_Block_ActivePoll",
                                                    "Mage_Paypal_Block_Logo",
                                                    "Mage_Tag_Block_Popular",
                                                    "Mage_Page_Block_Html_Footer"
                                                    );
     /**
     * Xml path google experiments enabled
     */
    const XML_PATH_ENABLED = 'google/analytics/experiments';
    /**
     * Group Collection
     */
    protected $_groupCollection;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * PageBuilder config node per website
     *
     * @var array
     */
    protected $_config = [];

    /**
     * Template filter factory
     *
     * @var \Magento\Catalog\Model\Template\Filter\Factory
     */
    protected $_templateFilterFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    protected $_readFactory;
     /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filesystem;
    /** @var UrlBuilder */
    protected $actionUrlBuilder;
    protected $_objectManager;



    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Customer\Model\Group $groupManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ReadFactory $readFactory
        ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_filterProvider = $filterProvider;
        $this->_groupCollection = $groupManager;
        $this->_readFactory = $readFactory;
        $this->_filesystem = $filesystem;
        $this->_objectManager = $objectManager;
        $this->actionUrlBuilder = $context->getUrlBuilder();
    }
    /**
     * Checks if Google Experiment is enabled
     *
     * @param string $store
     * @return bool
     */
    public function isGoogleExperimentEnabled($store = null)
    {
        return (bool)$this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $store);
    }

    
    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null, $default = "", $section = "vespagebuilder")
    {
        if($store) {
            $store = $this->_storeManager->getStore($store);
            $websiteId = $store->getWebsiteId();
        }

        $result = $this->scopeConfig->getValue(
            $section.'/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return ($result=="")?$default:$result;
    }

    public function filter($str)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }

    public function getCustomerGroups()
    {
        $data_array = array();

        $customer_groups = $this->_groupCollection->getCollection();

        foreach ($customer_groups as $item_group) {
            $data_array[] =  array('value' => $item_group->getId(), 'label' => $item_group->getCode());
        }
        
        return $data_array;

    }


    public function getShortCode($key, $alias = "", $settings = array()) {
        if($key) {
            $options = array();
            if($settings) {
                foreach($settings as $k => $v) {
                    if(trim($v)) {
                        $options[] = trim($k). '="'.trim($v).'"';
                    }
                }
            }

            $block_id = '';
            if($alias) {
                $block_id = 'block_id="'.trim($alias).'"';
            }
            return '{{widget type="'.trim($key).'" '.$block_id.' '.implode(" ", $options).'}}';
        }
        return  ;
    }

    public function generatePageBuilder($alias = "") {
        if($alias) {
            $short_code = $this->getShortCode("Ves\PageBuilder\Block\Widget\Block", $alias);
            return $this->filter($short_code);
        }
        return ;
    }

    public function runShortcode($short_code = "") {
        if($short_code) {
            return $this->filter($short_code);
        }
        return ;
    }

    public function checkModuleInstalled( $module_name = "") {
        return true;
    }


    public function getImageUrl($secure = false) {
        return;
    }

    /**
     * Handles CSV upload
     * @return string $filepath
     */
    public function getUploadedFile( $profile = "", $is_pagebuilder = false, $sub_folder = "") {
        $filepath = null;
        return $filepath;

    }

    public function getImportPath($theme = ""){
        $path = $this->getRootDirPath(). DS . 'cache'.DS;

        if (is_dir_writeable($path) != true) {
            mkdir ($path, '0744', $recursive  = true );
        } // end

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
    public function getBlockProfiles() {
        $path = $this->getRootDirPath()."pagebuilder".DIRECTORY_SEPARATOR."block_profiles".DIRECTORY_SEPARATOR;
        $dirs = array_filter(glob($path . '/*'), 'is_dir');
        $file_type = ".csv";
        $output = array();
        if($dirs) {
            $output["general"] = $this->getFileList($path, $file_type);
            foreach($dirs as $dir) {
                $file_name = basename( $dir );
                $tmp_path = $path.$file_name.DIRECTORY_SEPARATOR;
                if($tmp_output = $this->getFileList($tmp_path, $file_type)){
                    $output[$file_name] = $tmp_output;
                }

            }

        } else {
            $output = $this->getFileList($path, $file_type);
        }

        return $output;
    }
    /**
     *
     */
    public function getPageProfiles() {
        $path = $this->getRootDirPath()."pagebuilder".DIRECTORY_SEPARATOR."page_profiles".DIRECTORY_SEPARATOR;
        $dirs = array_filter(glob($path . '/*'), 'is_dir');
        $file_type = ".csv";
        $output = array();
        if($dirs) {
            $output["general"] = $this->getFileList($path, $file_type);
            foreach($dirs as $dir) {
                $file_name = basename( $dir );
                $tmp_path = $path.$file_name.DIRECTORY_SEPARATOR;
                if($tmp_output = $this->getFileList($tmp_path, $file_type)){
                    $output[$file_name] = $tmp_output;
                }

            }
        } else {
            $output = $this->getFileList($path, $file_type);
        }
        
        return $output;
    }


    public function getBlockProfilePath( $profile = "") {
        $path = $this->getRootDirPath()."pagebuilder".DIRECTORY_SEPARATOR."block_profiles".DIRECTORY_SEPARATOR.$profile.".csv";

        if(file_exists($path)) {
            return $path;
        }

        return false;
        
    }

    public function getPageProfilePath( $profile = "") {
        $path = $this->getRootDirPath()."pagebuilder".DIRECTORY_SEPARATOR."page_profiles".DIRECTORY_SEPARATOR.$profile.".csv";

        if(file_exists($path)) {
            return $path;
        }

        return false;
    }

    /**
     *
     */
    public function writeToCache( $folder, $file, $value, $e='css' ){
        $file = $folder  . preg_replace('/[^A-Z0-9\._-]/i', '', $file).'.'.$e ;
        if (file_exists($file)) {
            unlink($file);
        }
        file_put_contents($file, $value);
        @chmod($file, 0777);
    }
    
    public function autoBackupLayoutProfile($data = array(), $folder = "vespagebuilder") {
        $backup_dir = $this->getRootDirPath(DirectoryList::VAR_DIR)."{$folder}";
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir,0777,true);
        }
        if($data) {
            $filename = isset($data['alias']) ? str_replace([ '/', '\\' ], '__', $data['alias']) : rand() . time();
            $filename = $backup_dir.DIRECTORY_SEPARATOR.$filename."_bak_".time().".json";
            $content = isset($data['params'])?$data['params']:"";
            $widgets = isset($data['wpowidget'])?$data['wpowidget']:array();
            if($widgets && $content){
                $content = $this->insertWidgetShortcode($content, $widgets);
            }
            if($filename && $content) {
                if (file_exists($filename)) {
                    unlink($filename);
                }
                file_put_contents($filename, $content);
                @chmod($filename, 0777);
                return $filename;
            }
        }
        return false;
    }

    public function insertWidgetShortcode($json_content, $widgets = []){
        $json_obj = $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonDecode($json_content);
        $tmp_content = $json_content;
        if($json_obj && $widgets) {
            $json_array = $this->generateJsonArray($json_obj, $widgets);
            if($json_array) {
                $tmp_content = $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($json_array);
            }
        }

        return $tmp_content;
    }
    public function generateJsonArray($data_rows = [], $widgets = []){
        $output = [];
        foreach($data_rows as $row) {
            $tmp_row = $row;
            if(isset($row['cols']) && $row['cols']){
                foreach($row['cols'] as $i=>$col) {
                    if(isset($col['widgets']) && $col['widgets']){
                        foreach($col['widgets'] as $w=>$widget) {
                            if(isset($widgets[$widget['wkey']]) && isset($widgets[$widget['wkey']]['config'])) {
                                $tmp_row['cols'][$i]['widgets'][$w]['shortcode'] = $widgets[$widget['wkey']]['config'];
                            }
                        }
                    }
                    if(isset($col['rows']) && $col['rows']) {
                        $tmp_row['cols'][$i]['rows'] = $this->generateJsonArray($col['rows'], $widgets);
                    }
                }
            }
            $output[] = $tmp_row;
        }
        return $output;
    }
    protected function writeFile($content,$file,$type)
    {
        $path = "";
        return $path;
    }


    public function getSelectorGroups () {
        return array(   'body' => __('Body Content'),
                        'topbar' => __('TopBar'),
                        'header-main' => __('Header'),
                        'mainmenu' => __('MainMenu'),
                        'footer' => __('Footer'),
                        'footer-top' => __('Footer Top'),
                        'footer-center' => __('Footer Center'),
                        'footer-bottom' => __('Footer Bottom'),
                        'product' => __('Products'),
                        'powered' => __('Powered'),
                        'module-sidebar' => __('Modules in Sidebar'),
                        'module-block' => __('Module Blocks'),
                        'cart-block' => __('Cart Blocks'),
                        'checkout' => __('Checkout'),
                        'custom' => __('Custom')
                        );
    }

    public function getSelectorTypes () {
        return array('raw-text' => __('Text'),
                    'text' => __('Color Input'),
                    'image' => __('Image Pattern'),
                    'fontsize' => __('Font Size'),
                    'borderstyle' => __('Border Style'),
                    'textarea' => __('Custom Css Code')
                );
    }

    public function checkDragableBlock( $block_class_name = "") {
        $exists = in_array($block_class_name, self::$_list_dragable_blocks)?true:false;
        if(!$exists){
            foreach(self::$_list_dragable_blocks as $item) {
                if(strpos($item, $block_class_name)) {
                    $exists = true;
                    break;
                }
            }
        }
        return $exists;
    }

    public function checkEditableBlock( $block_class_name = "") {
        $exists = isset(self::$_list_editable_blocks[ $block_class_name])?true:false;
        if(!$exists){
            foreach(self::$_list_editable_blocks as $item) {
                if(strpos($item, $block_class_name)) {
                    $exists = true;
                    break;
                }
            }
        }
        return $exists;
    }

    public function subString( $text, $length = 100, $replacer ='...', $is_striped=true ){
            $text = ($is_striped==true)?strip_tags($text):$text;
            if(strlen($text) <= $length){
                return $text;
            }
            $text = substr($text,0,$length);
            $pos_space = strrpos($text,' ');
            return substr($text,0,$pos_space).$replacer;
    }

    public function getRootDirPath( $path_type = "") {
        $path_type = $path_type?$path_type:DirectoryList::PUB;
        return $this->_filesystem->getDirectoryRead($path_type)->getAbsolutePath();
    }

    public function getBackupLayouts($folder_name = "vespagebuilder") {
        $result = array();
        $file_ext = ".json";
        $folder = $this->getRootDirPath(DirectoryList::VAR_DIR)."{$folder_name}".DIRECTORY_SEPARATOR;

        $dirs = glob( $folder.'*'.$file_ext );
        if($dirs) { //load 
            foreach($dirs as $dir) {
                $file_name = basename( $dir );
                $filepath = $folder.$file_name;
                $file_name = str_replace(array(" ","."), "-", $file_name);
                $result[$file_name] = $this->readSampleFile($file_name, $filepath);
            }
        }

        return $result;
    }

    public function readSampleFile($file_name, $filepath = "") {
        $result = "";
        if($filepath) {
            $fileReader = $this->_readFactory->create($filepath, DriverPool::FILE);
            $result = $fileReader->readAll($file_name);
        }
        return $result;
    }

    public function getUrl($route = "", $params = []) {
        return $this->actionUrlBuilder->getUrl( $route, $params );
    }

    // HTML Minifier
    public function minify_html($input) {
        if(trim($input) === "") return $input;
        // Remove extra white-space(s) between HTML attribute(s)
        $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function($matches) {
            return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
        }, str_replace("\r", "", $input));
        // Minify inline CSS declaration(s)
        if(strpos($input, ' style=') !== false) {
            $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function($matches) {
                return '<' . $matches[1] . ' style=' . $matches[2] . $this->minify_css($matches[3]) . $matches[2];
            }, $input);
        }
        return preg_replace(
            array(
                // t = text
                // o = tag open
                // c = tag close
                // Keep important white-space(s) after self-closing HTML tag(s)
                '#<(img|input)(>| .*?>)#s',
                // Remove a line break and two or more white-space(s) between tag(s)
                '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
                '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
                // Remove HTML comment(s) except IE comment(s)
                '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
            ),
            array(
                '<$1$2</$1>',
                '$1$2$3',
                '$1$2$3',
                '$1$2$3$4$5',
                '$1$2$3$4$5$6$7',
                '$1$2$3',
                '<$1$2',
                '$1 ',
                '$1',
                ""
            ),
        $input);
    }

    // CSS Minifier => http://ideone.com/Q5USEF + improvement(s)
    public function minify_css($input) {
        if(trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                // Remove unused white-space(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                // Replace `:0 0 0 0` with `:0`
                '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                // Replace `background-position:0` with `background-position:0 0`
                '#(background-position):0(?=[;\}])#si',
                // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                '#(?<=[\s:,\-])0+\.(\d+)#s',
                // Minify string value
                '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                // Minify HEX color code
                '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                // Replace `(border|outline):none` with `(border|outline):0`
                '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                // Remove empty selector(s)
                '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
            ),
            array(
                '$1',
                '$1$2$3$4$5$6$7',
                '$1',
                ':0',
                '$1:0 0',
                '.$1',
                '$1$3',
                '$1$2$4$5',
                '$1$2$3',
                '$1:0',
                '$1$2'
            ),
        $input);
    }

    // JavaScript Minifier
    public function minify_js($input) {
        if(trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                // Remove white-space(s) outside the string and regex
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                // Remove the last semicolon
                '#;+\}#',
                // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                // --ibid. From `foo['bar']` to `foo.bar`
                '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
            ),
            array(
                '$1',
                '$1$2',
                '}',
                '$1$3',
                '$1.$3'
            ),
        $input);
    }
    

}