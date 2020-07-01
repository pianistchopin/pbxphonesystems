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
namespace Ves\PageBuilder\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Customer\Model\Session as CustomerSession;
/**
 * PageBuilder Model
 */
class Block extends \Magento\Framework\Model\AbstractModel
{   
    /**
     * Cunstruction regular expression
     */
    const CONSTRUCTION_PATTERN = '/{{([a-z]{0,10})(.*?)}}/si';
    /**
     * Block's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**
     * Block cache tag
     */
    const CACHE_BLOCK_TAG = 'ves_pagebuilder_block';

    /**
     * Page cache tag
     */
    const CACHE_TAG = 'ves_pagebuilder_page';

     /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_blockHelper;

    /**
    * @var \Magento\Backend\Helper\Data
    */
    protected $helperBackend;

    protected $_localeDate;

    protected $_core_write_connection;
    protected $_core_resource;
    protected $_objectManager;


    /**
     * @param \Magento\Framework\Model\Context                          $context                  
     * @param \Magento\Framework\Registry                               $registry                 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Ves\PageBuilder\Model\ResourceModel\Block|null                      $resource                 
     * @param \Ves\PageBuilder\Model\ResourceModel\Block\Collection|null           $resourceCollection       
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager    
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface      $localeDate             
     * @param \Magento\Framework\UrlInterface                           $url                      
     * @param \Ves\PageBuilder\Helper\Data                             $_blockHelper              
     * @param array                                                     $data                     
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CustomerSession $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ves\PageBuilder\Model\ResourceModel\Block $resource = null,
        \Ves\PageBuilder\Model\ResourceModel\Block\Collection $resourceCollection = null,
        \Magento\Framework\App\ResourceConnection $core_resource,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Ves\PageBuilder\Helper\Data $blockHelper,
        \Magento\Backend\Helper\Data $helperBackend,
        array $data = []
        ) {
        $this->customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->_blockHelper = $blockHelper;
        $this->_localeDate = $localeDate;
        $this->_core_resource = $core_resource;
        $this->_objectManager = $objectManager;
        $this->helperBackend = $helperBackend;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ves\PageBuilder\Model\ResourceModel\Block');
    }

    protected function getConnection()
    {
        if (!$this->_core_write_connection) {
            $this->_core_write_connection = $this->_core_resource->getConnection('core_write');
        }
        return $this->_core_write_connection;
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }


    public function getUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $route = $this->_blockHelper->getConfig('general_settings/route');
        $url_prefix = $this->_blockHelper->getConfig('general_settings/url_prefix');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        $url_suffix = $this->_blockHelper->getConfig('general_settings/url_suffix');
        return $url.$urlPrefix.$this->getUrlKey().$url_suffix;
    }

    /**
     * Retrive image URL
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $image;
        };
        return $url;
    }

    /**
     * Retrive thumbnail URL
     *
     * @return string
     */
    public function getThumbnailUrl()
    {
        $url = false;
        $thumbnail = $this->getThumbnail();
        if ($thumbnail) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $thumbnail;
        };
        return $url;
    }
    public function getBlockById($block_id = "", $is_page = false) {
        $customer_group_id = (int)$this->customerSession->getCustomerGroupId();
        if($block_id) {
            $today_date_time  = new \DateTime('today');
            $today_date = $this->_localeDate->formatDateTime(
                                $today_date_time,
                                \IntlDateFormatter::MEDIUM,
                                \IntlDateFormatter::MEDIUM
                            );
            $todayDateTime = strtotime($today_date);
            $todayDate = date("Y-m-d", $todayDateTime);

            $store_id = (int)$this->_storeManager->getStore()->getId();
            $collection = $this->getCollection()
                                ->addFieldToFilter('block_id', $block_id)
                                ->addFieldToFilter('status', 1)
                                ->addFieldToFilter('show_from', array('or'=> array(
                                    0 => array('date' => true, 'lt' => $todayDate),
                                    1 => array('is' => new \Zend_Db_Expr('null')))
                                ), 'left')
                                ->addFieldToFilter('show_to', array('or'=> array(
                                    0 => array('date' => true, 'gteq' => $todayDate),
                                    1 => array('is' => new \Zend_Db_Expr('null')))
                                ), 'left');

            if($is_page) {
                $collection->addStoreFilter($store_id);
                $collection->addFieldToFilter('block_type', "page");
            }
            $block_entity = $collection->getFirstItem();
                    
            if($block_entity) {
                $this->getResource()->walkAfterLoad($block_entity);
                $customer_group = $block_entity->getCustomerGroup();
                $array_groups = explode(",",$customer_group);
                if($array_groups && in_array(0, $array_groups)){
                    return $block_entity;
                } elseif( $array_groups && in_array($customer_group_id, $array_groups)) {
                    return $block_entity;
                }
            }
            
        }
        return null;
    }
    public function getBlockByAlias($alias = "", $is_page = false) {
        $customer_group_id = (int)$this->customerSession->getCustomerGroupId();
        if($alias) {
            $today_date_time  = new \DateTime('today');
            $today_date = $this->_localeDate->formatDateTime(
                                $today_date_time,
                                \IntlDateFormatter::MEDIUM,
                                \IntlDateFormatter::MEDIUM
                            );
            $todayDateTime = strtotime($today_date);
            $todayDate = date("Y-m-d", $todayDateTime);


            $collection = $this->getCollection()
                                ->addFieldToFilter('alias', $alias)
                                ->addFieldToFilter('status', 1)
                                ->addFieldToFilter('show_from', array('or'=> array(
                                    0 => array('date' => true, 'lt' => $todayDate),
                                    1 => array('is' => new \Zend_Db_Expr('null')))
                                ), 'left')
                                ->addFieldToFilter('show_to', array('or'=> array(
                                    0 => array('date' => true, 'gteq' => $todayDate),
                                    1 => array('is' => new \Zend_Db_Expr('null')))
                                ), 'left');

            if($is_page) {
                $collection->addFieldToFilter('block_type', "page");
            }
            $block_entity = $collection->getFirstItem();
                    
            if($block_entity) {
                $this->getResource()->walkAfterLoad($block_entity);
                $customer_group = $block_entity->getCustomerGroup();
                $array_groups = explode(",",$customer_group);
                if($array_groups && in_array(0, $array_groups)){
                    return $block_entity;
                } elseif( $array_groups && in_array($customer_group_id, $array_groups)) {
                    return $block_entity;
                }
            }
            
        }
        return null;
    }

    public function isExpiredPage( $block_profile = null) {
        $expired = false;
        if($block_profile) {
            $today_date_time  = new \DateTime('today');

            $todayDate = $this->_localeDate->formatDateTime(
                        $today_date_time,
                        \IntlDateFormatter::MEDIUM,
                        \IntlDateFormatter::MEDIUM
                    );

            $todayDateTime = strtotime($todayDate);
            
            $date_to = $block_profile->getShowTo();
            if($date_to) {
                $date_to = strtotime($date_to);
            } else {
                $date_to = 0;
            }

            if($date_to > 0 && $date_to < $todayDateTime) {
                $expired = true;
            }
        }
        return $expired;
    }

    public function isCommingSoonPage( $block_profile = null) {
        $comming_soon = false;
        if($block_profile) {
            $today_date_time  = new \DateTime('today');

            $todayDate = $this->_localeDate->formatDateTime(
                        $today_date_time,
                        \IntlDateFormatter::MEDIUM,
                        \IntlDateFormatter::MEDIUM
                    );

            $todayDateTime = strtotime($todayDate);

            $date_from = $block_profile->getShowFrom();

            if($date_from) {
                $date_from = strtotime($date_from);
            } else {
                $date_from = 0;
            }

            if($date_from > $todayDateTime ) {
                $comming_soon = true;
            }
        }
        return $comming_soon;
    }


    public function isPrivatePage( $block_profile = null) {
        $private = false;
        if($block_profile ) {
            $customer_group_id = (int)$this->customerSession->getCustomerGroupId();
            $customer_group =  $block_profile->getCustomerGroup();
            $array_groups = explode(",",$customer_group);

            if($array_groups && !in_array(0, $array_groups) && !in_array($customer_group_id, $array_groups)){
                $private = true;
            }
        }
        return $private;
    }
    public function checkBlockProfileAvailable( $block_profile = null ){
        $checked = true;
        if($block_profile) {
            if($block_profile->getStatus() != "1") {
                $checked = false;   
            } else {
                $customer_group_id = (int)$this->customerSession->getCustomerGroupId();
                $customer_group =  $block_profile->getCustomerGroup();
                $array_groups = explode(",",$customer_group);

                if($array_groups && (!in_array($customer_group_id, $array_groups) || (1==count($array_groups) && $array_groups[0] == 0 && $customer_group_id))){
                    $checked = false;
                } else {
                    $today_date_time  = new \DateTime('today');

                    $todayDate = $this->_localeDate->formatDateTime(
                                $today_date_time,
                                \IntlDateFormatter::MEDIUM,
                                \IntlDateFormatter::MEDIUM
                            );

                    $todayDateTime = strtotime($todayDate);

                    $date_from = $block_profile->getShowFrom();

                    if($date_from) {
                        $date_from = strtotime($date_from);
                    } else {
                        $date_from = 0;
                    }
                    
                    $date_to = $block_profile->getShowTo();
                    if($date_to) {
                        $date_to = strtotime($date_to);
                    } else {
                        $date_to = 0;
                    }

                    if($date_from > $todayDateTime || ($date_to > 0 && $date_to < $todayDateTime)) {
                        $checked = false;
                    }
                }
                
            }
        }
        return $checked;
    }

    public function loadCMSBlock($field_value, $field_name = "identifier", $stores = array(), $withAdmin = true) {
        $is_single_store = false;
        $tmp_stores = $stores;
        $isSingleStoreMode = $this->_storeManager->isSingleStoreMode();
        if($isSingleStoreMode || !$stores) {
            $is_single_store = true;
            if ($stores instanceof \Magento\Store\Model\Store) {
                $stores = [$stores->getId()];
            }

            if (!is_array($stores)) {
                $stores = [$stores];
            }

            if ($withAdmin) {
                $stores[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            }
        } else {
            if (!is_array($stores)) {
                $stores = [$stores];
            }
        }

        $readConnection = $this->getConnection();
        $cms_block_table = $this->_core_resource->getTableName('cms_block');
        $cms_block_store_table = $this->_core_resource->getTableName('cms_block_store');;
        $core_store = $this->_core_resource->getTableName('store');;

        $select = $readConnection->select()
            ->from(['cp' => $cms_block_table])
            ->join(
                ['cps' => $cms_block_store_table],
                'cp.block_id = cps.block_id',
                [])
            ->where('cp.'.$field_name.' = ?', $field_value)
            ->where('cps.store_id IN (?)', $stores)
            ->limit(1);

        $model = $this->_objectManager->create('Magento\Cms\Model\Block');

        if($block = $readConnection->fetchRow($select)) {
            $block_id = $block['block_id'];
            $block = $model->load($block_id);
        } elseif($is_single_store || (!$is_single_store && !$tmp_stores)) {
            $block = $model->load($field_value, $field_name);
        } else {
            $block = $model->load(0);
        }
        return $block;
        
    }

    public function loadCMSPage($field_value, $field_name = "identifier", $stores = array(), $withAdmin = true) {
        $is_single_store = false;
        $tmp_stores = $stores;
        $isSingleStoreMode = $this->_storeManager->isSingleStoreMode();
        if($isSingleStoreMode || !$stores) {
            $is_single_store = true;
            if ($stores instanceof \Magento\Store\Model\Store) {
                $stores = [$stores->getId()];
            }

            if (!is_array($stores)) {
                $stores = [$stores];
            }

            if ($withAdmin) {
                $stores[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            }
        } else {
            if (!is_array($stores)) {
                $stores = [$stores];
            }
        }

        $readConnection = $this->getConnection();
        $cms_page_table = $this->_core_resource->getTableName('cms_page');
        $cms_page_store_table = $this->_core_resource->getTableName('cms_page_store');;
        $core_store = $this->_core_resource->getTableName('store');;

        $select = $readConnection->select()
            ->from(['cp' => $cms_page_table])
            ->join(
                ['cps' => $cms_page_store_table],
                'cp.page_id = cps.page_id',
                [])
            ->where('cp.'.$field_name.' = ?', $field_value)
            ->where('cps.store_id IN (?)', $stores)
            ->limit(1);

        $model = $this->_objectManager->create('Magento\Cms\Model\Page');

        if($page = $readConnection->fetchRow($select)) {
            $page_id = $page['page_id'];
            $page = $model->load($page_id);
        } elseif($is_single_store || (!$is_single_store && !$tmp_stores)) {
            $page = $model->load($field_value, $field_name);
        } else {
            $page = $model->load(0);
        }
        return $page;
        
    }

    public function getBanner() {
        return  $this->getImage();
    }

    public function getListWidgets() {
        $widgets = [];
        $widgets = $this->getResource()->lookupWidgets(0);
        return $widgets;
    }

    public function lookupWidgets($pageId = 0) {
        return $this->getResource()->lookupWidgets($pageId);
    }

    public function getWidgetParameters($value)
    {
        $params = [];
        if($value) {
            $tokenizer = new \Magento\Framework\Filter\Template\Tokenizer\Parameter();
            $tokenizer->setString($value);
            $params = $tokenizer->tokenize();
            foreach ($params as $key => $value) {
                if (substr($value, 0, 1) === '$') {
                    $params[$key] = $this->getVariable(substr($value, 1), null);
                }
            }
        }
        return $params;
    }
    public function parserShortcode($shortcode) {
        $widget_array = [];
        if($shortcode) {
            if(preg_match_all(self::CONSTRUCTION_PATTERN, $shortcode, $constructions, PREG_SET_ORDER)) {
                foreach($constructions as $index=>$construction) {
                    $widget_array = $this->getWidgetParameters($construction[2]);
                }
            }
        }
        return $widget_array;
    }


    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }


    public function getWidgetInfo($widget_type, $shortcode ) {
        $widget = [ 
                    "title"         => "",
                    "description"   => "",
                    "icon"          => "",
                    "banner"        => "",
                    "created_at"    => "",
                    "module"        => ""
                    ];

        switch ($widget_type) {
               case 'Ves/PageBuilder/Block/Widget/Builder':
                   $widget_array = $this->parserShortcode($shortcode);
                   if(isset($widget_array['block_id']) && $widget_array['block_id']) {
                        $block_id = (int)$widget_array['block_id'];
                        $element_model = $this->load($block_id);

                        $element_url = $this->helperBackend->getUrl("*/blockbuilder/edit", ["block_id"=>$block_id]);

                        $widget['title'] = __("Element Profile: ").'<a href="'.$element_url.'" target="_BLANK" onclick="window.location=\''.$element_url.'\';" title="'.__("Edit Element Profile").'"><span>'.$element_model->getTitle().'</span></a>';
                        $widget['description'] = $element_model->getDescription();
                        $widget['description'] = strip_tags($widget['description']);
                        $widget['description'] = trim($widget['description']);
                        $widget['banner'] = $element_model->getBanner();
                        $widget['created_at'] = $element_model->getCreated();
                   }
                   break;
               case 'Magento/Cms/Block/Widget/Block':
                   $widget_array = $this->parserShortcode($shortcode);
                   if(isset($widget_array['block_id']) && $widget_array['block_id']) {
                        $block_id = (int)$widget_array['block_id'];
                        $model = $this->_objectManager->create('Magento\Cms\Model\Block');
                        $cms_block_model = $model->load($block_id);

                        $cms_block_url = $this->helperBackend->getUrl("cms/block/edit", ["block_id"=>$block_id]);

                        $widget['title'] = __("CMS Static Block: ").'<a href="'.$cms_block_url.'" target="_BLANK" onclick="window.location=\''.$cms_block_url.'\';" title="'.__("Edit CMS Static Block").'"><span>'.$cms_block_model->getTitle().'</span></a>';
                        $widget['description'] = $cms_block_model->getContent();
                        $widget['description'] = strip_tags($widget['description']);
                        $widget['description'] = trim($widget['description']);
                        $widget['created_at'] = $cms_block_model->getCreationTime();
                   }
                   break;
                case 'Ves/BaseWidget/Block/Widget/Image':
                    $widget_array = $this->parserShortcode($shortcode);

                    $title = isset($widget_array['title'])?(' - '.$widget_array['title']):'';
                    $image_file = isset($widget_array['file'])?$widget_array['file']:'';
                    if ($image_file) {
                        if(!preg_match("/^http\:\/\/|https\:\/\//", $image_file)) {
                            $image_file = $this->getBaseMediaUrl() . $image_file;
                        }
                    }
                    $widget['title'] = __("Single Image").$title;
                    if($image_file) {
                        $widget['icon'] = '<img src="'.$image_file.'" class="widget-icon-img" alt="'.__("Logo").'"/>';
                    }

                    break;
                case 'Ves/BaseWidget/Block/Widget/Ourservice':
                    $widget_array = $this->parserShortcode($shortcode);

                    $title = isset($widget_array['title'])?(' - '.$widget_array['title']):'';
                    $icon_class = isset($widget_array['icon_class'])?$widget_array['icon_class']:'';
                    $image_file = isset($widget_array['file'])?$widget_array['file']:'';
                    if ($image_file) {
                        if(!preg_match("/^http\:\/\/|https\:\/\//", $image_file)) {
                            $image_file = $this->getBaseMediaUrl() . $image_file;
                        }
                    }
                    $html = isset($widget_array['content_html'])?$widget_array['content_html']:'';
                    $html = str_replace(" ", "+", $html);
                    if(is_string($html) && $this->isBase64Encoded($html)) {
                        $html = base64_decode($html);
                    }
                    $html = strip_tags($html);
                    $html = trim($html);

                    $widget['title'] = __("Our Service").$title;
                    $widget['description'] = $html;
                    if($icon_class) {
                        $widget['icon'] = '<i class="fa '.$icon_class.'" style="font-size:24px"></i>';
                    } elseif($image_file){
                        $widget['icon'] = '<img src="'.$image_file.'" class="widget-icon-img" alt="'.__("Logo").'"/>';
                    }

                    break;
                case 'Ves/BaseWidget/Block/Widget/Html':
                    $widget_array = $this->parserShortcode($shortcode);

                    $title = isset($widget_array['title'])?(' - '.$widget_array['title']):'';
                    $html = isset($widget_array['html'])?$widget_array['html']:'';
                    $html = str_replace(" ", "+", $html);
                    if(is_string($html) && $this->isBase64Encoded($html)) {
                        $html = base64_decode($html);
                    }
                    $html = strip_tags($html,"<a><b><strong><span><ul><li><i><u>");
                    $html = trim($html);

                    $widget['title'] = __("Html").$title;
                    $widget['description'] = $html;

                    break;
                case 'Ves/BaseWidget/Block/Widget/ContentHtml':
                    $widget_array = $this->parserShortcode($shortcode);

                    $title = isset($widget_array['title'])?(' - '.$widget_array['title']):'';
                    $html = isset($widget_array['html'])?$widget_array['html']:'';
                    $html = str_replace(" ", "+", $html);
                    if(is_string($html) &&$this->isBase64Encoded($html)) {
                        $html = base64_decode($html);
                    }
                    $html = strip_tags($html,"<a><b><strong><span><ul><li><i><u>");
                    $html = trim($html);

                    $widget['title'] = __("Simple Content Html").$title;
                    $widget['description'] = $html;

                    break;
                case 'Ves/BaseWidget/Block/Widget/Heading':
                    $widget_array = $this->parserShortcode($shortcode);

                    $heading_tag = (isset($widget_array['heading_tag'])&&$widget_array['heading_tag'])?$widget_array['heading_tag']:'h3';
                    $html = isset($widget_array['content'])?$widget_array['content']:'';
                    $html = str_replace(" ", "+", $html);
                    if(is_string($html) && $this->isBase64Encoded($html)) {
                        $html = base64_decode($html);
                    }
                    
                    $html = strip_tags($html,"<a><b><strong><span><ul><li><i><u>");
                    $html = trim($html);

                    $widget['title'] = __("Heading Tag");
                    $widget['description'] = "<".$heading_tag.">".$html."</".$heading_tag.">";

                    break;
               default:
                   # code...
                   break;
        }           
        return $widget;
    }
    public function isBase64Encoded($data) {
        if(base64_encode($data) === $data) return false;
        if(base64_encode(base64_decode($data)) === $data){
            return true;
        }
        if (!preg_match('~[^0-9a-zA-Z+/=]~', $data)) {
            $check = str_split(base64_decode($data));
            $x = 0;
            foreach ($check as $char) if (ord($char) > 126) $x++;
            if ($x/count($check)*100 < 30) return true;
        }
        $decoded = base64_decode($data);
        // Check if there are valid base64 characters
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $data)) return false;
        // if string returned contains not printable chars
        if (0 < preg_match('/((?![[:graph:]])(?!\s)(?!\p{L}))./', $decoded, $matched)) return false;
        if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) return false;

        
        return false;
    }
}