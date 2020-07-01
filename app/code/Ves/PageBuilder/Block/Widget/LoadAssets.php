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
namespace Ves\PageBuilder\Block\Widget;

class LoadAssets extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

	protected $_blockModel;
	protected $_dataFilterHelper;
	protected $_layout;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\PageBuilder\Helper\Data $dataHelper,
		array $data = []
		) {

		parent::__construct($context, $data);

		$_pageConfig = $context->getPageConfig();
		$this->_layout = $context->getLayout();
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;

		if(isset($data['css_path'])) {
			$this->setData("css_path", $data['css_path']);
		}

		if(isset($data['css_media_condition'])) {
			$this->setData("css_media_condition", $data['css_media_condition']);
		}

		if(isset($data['js_path'])) {
			$this->setData("js_path", $data['js_path']);
		}

		$my_template = "Ves_PageBuilder::common/assets.phtml";

		if($this->hasData("template") && $this->getData("template")) {
        	$my_template = $this->getData("template");
        }

        if(isset($data['template']) && $data['template']) {
        	$my_template = $data['template'];
        }
		
		$this->setTemplate($my_template);

		//Load Css File on head tag
		$css_path = $this->getConfig("css_path");
		$css_media_condition = $this->getConfig("css_media_condition");
		$css_media_condition = $css_media_condition?$css_media_condition:"all";

		if($css_path){ //Check if have css path 
			$parsed = parse_url($css_path);
	        if (!empty($parsed['scheme'])) { //Load external css file
	           $_pageConfig->addRemotePageAsset($css_path, 'css', [
				"attributes" => [ "media" => $css_media_condition ]
				]);
	        } else { //Load internal css file
	        	$_pageConfig->addPageAsset($css_path,[
				"attributes" => [ "media" => $css_media_condition ]
				]);
	        }
		}
	}
	public function getConfig($key, $default = NULL){
		if($this->hasData($key)){
			return $this->getData($key);
		}
		return $default;
	}
	public function getDataFilterHelper() {
		return $this->_dataFilterHelper;
	}
	public function getLayout() {
		return $this->_layout;
	}

	public function getCustomCss(){
		$custom_css = $this->getConfig("content");
		if($custom_css) {
			$custom_css = str_replace(" ", "+", $custom_css);
			$custom_css = base64_decode($custom_css);
		}
		return $custom_css;
	}
}