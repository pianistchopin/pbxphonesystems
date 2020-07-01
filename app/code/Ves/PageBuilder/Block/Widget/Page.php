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
namespace Ves\PageBuilder\Block\Widget;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Json\EncoderInterface;
use Magento\Customer\Model\Context;

class Page extends AbstractWidget
{


    /**
     * Block Collection
     */
    protected $_blockCollection;

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
    protected $_page_builder_profile = null;
    protected $jsonEncoder;
    protected $_blockModel;

     /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    protected $_customerSession;

     /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $response;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context         
     * @param \Magento\Framework\Registry                      $registry 
     * @param \Magento\Framework\Filesystem                    $filesystem,
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager,       
     * @param \Ves\PageBuilder\Helper\Data                    $blockHelper     
     * @param \Ves\PageBuilder\Model\Block                    $blockCollection 
     * @param array                                            $data            
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
       /*\Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,*/
        \Ves\PageBuilder\Helper\Data $blockHelper,
        \Ves\PageBuilder\Model\Block $blockCollection,
        \Magento\Framework\App\Http\Context $httpContext,
        EncoderInterface $jsonEncoder,
        \Ves\PageBuilder\Helper\MobileDetect $mobileDetectHelper,
        \Magento\Cms\Model\Block $blockModel,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
        ) {
        $this->_blockCollection = $blockCollection;
        $this->_blockHelper = $blockHelper;
        $this->_coreRegistry = $registry;
        $this->_blockModel = $blockModel;
        /*$this->_storeManager = $storeManager;*/
        /*$this->_filesystem = $filesystem;*/
        $this->jsonEncoder = $jsonEncoder;
        $this->_customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->response = $response;

        parent::__construct($context, $blockHelper, $mobileDetectHelper, $data);

        $this->setTemplate("pagebuilder/default.phtml");
    }

    
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags' => [\Ves\PageBuilder\Model\Block::CACHE_TAG,
            ], ]);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $block_id = $this->getConfig("block_id");
        $block_id = $block_id?$block_id:0;
        $code = $this->getConfig('code');

        $device = "desktop";

        if($this->getMobileDetect()->isMobile() && !$this->getMobileDetect()->isTablet()) { //If current are mobile devices
            $device = "mobile";
        } elseif($this->getMobileDetect()->isTablet()) { //If current are mobile devices
            $device = "tablet";
        }

        $conditions = $code.".".$block_id.".".$device;

        return [
        'VES_PAGEBUILDER_BUILDER_WIDGET',
        $this->_storeManager->getStore()->getId(),
        $this->_design->getDesignTheme()->getId(),
        $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
        $conditions
        ];
    }

    public function getJsonEncoder() {
        return $this->jsonEncoder;
    }

    public function _prepareLayout()
    {
        
        if(!$this->_blockHelper->getConfig('general/show')) return parent::_prepareLayout();
       
        $block_id = $this->getConfig("block_id");
        $block_id = $block_id?$block_id:0;
        $code = $this->getConfig('code');
        $this->_banner = null;
        if($block_id) {
            $this->_banner  = $this->_blockCollection->getBlockById( $block_id, true );
        }

        if(!$this->_banner && $code) {
            $this->_banner = $this->_blockCollection->getBlockByAlias($code, true);
        }

        $this->_page_builder_profile = $this->_banner;

        if($this->_banner && !$this->_blockCollection->checkBlockProfileAvailable($this->_banner)) {
            $settings = $this->_banner->getSettings();
            $settings = unserialize($settings);
            $this->setSettings($settings);
            $this->_banner = null;
        }

        if($this->_banner) {
            $params = $this->_banner->getParams();
            $params = \Zend_Json::decode($params);

            $block_widgets = $this->_banner->getWidgets();

            $settings = $this->_banner->getSettings();
            $settings = unserialize($settings);
            $this->setSettings($settings);

            $this->assign("layouts", $params);
            $this->assign("block_widgets", $block_widgets);
            $this->assign("settings", $settings);
            $this->assign("is_container", $this->_banner->getContainer());
            $this->assign("class", $this->_banner->getPrefixClass());
            $this->assign("show_title", $this->getConfig("show_title"));
            $this->assign("disable_wrapper", $this->getConfig("disable_wrapper"));
            $this->assign("heading", $this->_banner->getTitle());

            if(1 == $this->_banner->getContainer()) {
                $this->setTemplate("pagebuilder/default_container.phtml");
            }
        } else {
            if($this->_page_builder_profile) {
                if($this->_blockCollection->isPrivatePage($this->_page_builder_profile)) {
                    $this->initPrivatePage();
                    $this->setTemplate("pagebuilder/not_available_content.phtml");
                } elseif($this->_blockCollection->isExpiredPage($this->_page_builder_profile)) {
                    $this->initExpiredPage();
                    $this->setTemplate("pagebuilder/not_available_content.phtml");
                } elseif($this->_blockCollection->isCommingSoonPage($this->_page_builder_profile)) {
                    $this->initCommingSoonPage();
                    $this->setTemplate("pagebuilder/not_available_content.phtml");
                }
            }
        }
        return parent::_prepareLayout();
    }

    public function _toHtml() {
        $settings = $this->getSettings();
        $html = "";

        if($settings && isset($settings['enable_wrapper']) && $settings['enable_wrapper'] == 1) {
            $wrapper_class = (isset($settings['select_wrapper_class'])?$settings['select_wrapper_class']." ":'');
            $wrapper_class .= (isset($settings['wrapper_class'])?$settings['wrapper_class']:'');

            if(isset($this->_banner) && $this->_banner) {
                $wrapper_class .= " ".$this->_banner->getAlias();
            }
            $html = '<div class="'.$wrapper_class.'">'.parent::_toHtml().'</div>';
        } else {
            $html = parent::_toHtml();
        }

        if($this->_blockHelper->getConfig('general/minify_html')) {
            $html = $this->_blockHelper->minify_html( $html );
        }

        return $html;

    }

    public function isLoggedIn(){
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    public function initPrivatePage(){
        $enable_login_form = $this->_blockHelper->getConfig('private_page_setting/enable_login_form');
        $cms_block_id = $this->_blockHelper->getConfig('private_page_setting/load_cms_block');
        $redirect_url = $this->_blockHelper->getConfig('private_page_setting/redirect_url');

        //Load page profile settings
        $profile_enable_login_form = $this->_page_builder_profile->getData("private_enable_login_form");
        $profile_cms_block_id = $this->_page_builder_profile->getData("private_cms_block");
        $profile_redirect_url = $this->_page_builder_profile->getData("private_redirect_url");

        //Override module settings
        if($profile_enable_login_form) {
            $enable_login_form = ($profile_enable_login_form == 1)?$profile_enable_login_form:0;
        }
        if($profile_cms_block_id) {
            $cms_block_id = $profile_cms_block_id;
        }
        if($profile_redirect_url) {
            $redirect_url = $profile_redirect_url;
        }
        if($redirect_url) { //redirect to url
            $parsed = parse_url($redirect_url);
            if (!empty($parsed['scheme'])) {
                $this->response->setRedirect($redirect_url);
            }
        }

        $cms_block_html = "";
        $login_form_block = null;
        $login_additioninfo_block = null;
        $customer_new_block = null;
        if($cms_block_id) {
            $cms_block_html = $this->_blockModel->load($cms_block_id)->getContent();
            $cms_block_html = $this->_blockHelper->filter($cms_block_html);
        }

        if(($enable_login_form == 1) && !$this->isLoggedIn()) {
            //Login form block
            $login_form_block = $this->getLayout()->createBlock(
                'Magento\Customer\Block\Form\Login',
                'customer_form_login'
            );
            $login_form_block->setTemplate('form/login.phtml');
            //Login additional information block
            $login_additioninfo_block = $this->getLayout()->createBlock(
                'Magento\Framework\View\Element\Template',
                'form_additional_info_customer'
            );
            $login_additioninfo_block->setTemplate('Magento_Customer::additionalinfocustomer.phtml');
            $login_form_block->setChild("form_additional_info", $login_additioninfo_block);

            //Customer New block
            $customer_new_block = $this->getLayout()->createBlock(
                'Magento\Customer\Block\Form\Login\Info',
                'customer.new'
            );
            $customer_new_block->setTemplate('newcustomer.phtml');
        }

        $this->assign("cms_block", $cms_block_html);
        $this->assign("login_form_block", $login_form_block);
        $this->assign("customer_new_block", $customer_new_block);
    }

    public function initExpiredPage(){
        $cms_block_id = $this->_blockHelper->getConfig('expire_page_setting/load_cms_block');
        $redirect_url = $this->_blockHelper->getConfig('expire_page_setting/redirect_url');

        //Load page profile settings
        $expired_cms_block_id = $this->_page_builder_profile->getData("expired_cms_block");
        $expired_redirect_url = $this->_page_builder_profile->getData("expired_redirect_url");

        //Override module settings
        if($expired_cms_block_id) {
            $cms_block_id = $expired_cms_block_id;
        }
        if($expired_redirect_url) {
            $redirect_url = $expired_redirect_url;
        }

        $this->initCmsBlockPlaceHolder($cms_block_id, $redirect_url);
    }

    public function initCommingSoonPage(){
        $cms_block_id = $this->_blockHelper->getConfig('commingsoon_page_setting/load_cms_block');
        $redirect_url = $this->_blockHelper->getConfig('commingsoon_page_setting/redirect_url');

        //Load page profile settings
        $comming_cms_block_id = $this->_page_builder_profile->getData("comming_cms_block");
        $comming_redirect_url = $this->_page_builder_profile->getData("comming_redirect_url");

        //Override module settings
        if($comming_cms_block_id) {
            $cms_block_id = $comming_cms_block_id;
        }
        if($comming_redirect_url) {
            $redirect_url = $comming_redirect_url;
        }
        $this->initCmsBlockPlaceHolder($cms_block_id, $redirect_url);
    }

    public function initCmsBlockPlaceHolder($cms_block_id, $redirect_url = ""){
        if($redirect_url) { //redirect to url
            $parsed = parse_url($redirect_url);
            if (!empty($parsed['scheme'])) {
                $this->response->setRedirect($redirect_url);
            }
        }
        $cms_block_html = "";
        if($cms_block_id) { 
            $cms_block_html = $this->_blockModel->load($cms_block_id)->getContent();
            $cms_block_html = $this->_blockHelper->filter($cms_block_html);
        }

        $this->assign("cms_block", $cms_block_html);
    }

    public function renderWidgetShortcode( $shortcode = "") {
        if($shortcode) {
            return $this->_blockHelper->filter($shortcode);
        }
        return;
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
}