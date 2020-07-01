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
 * @copyright  Copyright (c) 2017 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\PageBuilder\Block;

class Preview extends \Ves\PageBuilder\Block\Widget\AbstractWidget
{
    /**
     * @var \Ves\PageBuilder\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Ves\PageBuilder\Model\Block
     */
    protected $_blockmodel;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context            
     * @param \Magento\Framework\Registry                      $registry           
     * @param \Ves\PageBuilder\Helper\Data                        $helper             
     * @param \Ves\PageBuilder\Model\Menu                         $blockmodel               
     * @param array                                            $data               
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ves\PageBuilder\Helper\Data $helper,
        \Ves\PageBuilder\Model\Block $blockmodel,
        \Ves\PageBuilder\Helper\MobileDetect $mobileDetectHelper,
        array $data = []
        ) {

        $this->_helper       = $helper;
        $this->_blockmodel         = $blockmodel;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $helper, $mobileDetectHelper, $data);
    }

    public function _toHtml() {
        if(!$this->_blockHelper->getConfig('general/show')) return;
        if(!$this->_blockHelper->getConfig('general/enable_preview')) return;    
        $this->setTemplate("Ves_PageBuilder::blockbuilder/default.phtml");
        $blockprofile = $this->getBlockProfile();
        if($blockprofile && !$this->_blockmodel->checkBlockProfileAvailable($blockprofile)) {
            $blockprofile = null;
        }
        if($blockprofile) {
            $block_id = $blockprofile->getId();
            $code = $blockprofile->getAlias();
            $params = $blockprofile->getParams();
            $params = \Zend_Json::decode($params);
            $block_widgets = $blockprofile->getWidgets();
            $this->assign("block_id", "block-".$block_id." ".$code);
            $this->assign("layouts", $params);
            $this->assign("block_widgets", $block_widgets);
            $this->assign("is_container", $blockprofile->getContainer());
            $this->assign("class", $blockprofile->getPrefixClass());
            $this->assign("show_title", 0);
            $this->assign("disable_wrapper", 1);
            $this->assign("heading", __("Preview"));
            return parent::_toHtml();
        }

        return;
    }

    public function getBlockProfile() {
        $blockprofile = $this->_coreRegistry->registry('current_blockprofile');
        return $blockprofile;
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
    public function getBlockRowFilePath() {
        if(!$this->_row_phtml) {
            $module_dir = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath();
            $file_path = "code/Ves/PageBuilder/view/frontend/templates/blockbuilder/row.phtml";
            $file_path = str_replace("/", DIRECTORY_SEPARATOR , $file_path);

            $row_phtml_file_path = $module_dir.$file_path;
            if(file_exists($row_phtml_file_path)){
                $this->_row_phtml = $row_phtml_file_path;
            }
        }
        return $this->_row_phtml;
    }
}