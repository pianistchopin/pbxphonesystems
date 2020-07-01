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
 * @package    Ves_BaseWidget
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Block\Widget;
use Ves\BaseWidget\Block\AbstractWidget;

class Customblock extends AbstractWidget{

	protected $_blockModel;
	protected $_dataFilterHelper;
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		

		if($this->hasData("template")) {
        	$my_template = $this->getData("template");
        }else{
 			$my_template = "widget/customblock.phtml";
 		}
 		$this->setTemplate($my_template);
	}

	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$widget_heading = $this->getConfig("title");
		$block_type = $this->getConfig("block_type");
		$block_type = trim($block_type);
		$block_name = $this->getConfig("block_name");
		$block_name = trim($block_name);
		$block_params = $this->getConfig("block_params");
		$block_params = trim($block_params);
		$block_params = str_replace(" ","+", $block_params);

		if (base64_decode($block_params, true) == true){
			$block_params = base64_decode($block_params);
		} else {
			$block_params = $this->getConfig("block_params");
		}

		$params = explode("\n", $block_params);
		$template = "";
		if($params) {
			$tmp = array();
			foreach($params as $key=>$val) {
				$val = trim($val);
				if($val) {
					$tmp_array = explode("=", $val);
					if(isset($tmp_array[0])) {
						$tmp[trim($tmp_array[0])] = isset($tmp_array[1])?trim($tmp_array[1]):"";
						if("template" == trim($tmp_array[0]) && $tmp[trim($tmp_array[0])]) {
							$template = isset($tmp_array[1])?trim($tmp_array[1]):"";
							unset($tmp[trim($tmp_array[0])]);
						}
					}
				}
			}
			$params = $tmp;
		}
		$block_html = "";
		if($block_type) {
			$block = $this->getLayout()->createBlock($block_type, $block_name, $params);
			if($template) {
				$block_html = $block->setTemplate($template)->toHtml();
			} else {
				$block_html = $block->toHtml();
			}
		}

		$this->assign('widget_heading', $widget_heading);
		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('block_html', $block_html);

		return parent::_toHtml();
	}
}