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
class Facebook extends AbstractWidget{

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
		$this->setTemplate("widget/facebook.phtml");
	}

	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$url = $this->getConfig('facebook_url');
		$url = str_replace(" ", "+",$url);
		$url = urlencode($url);
		$app_id = $this->getConfig('app_id', '1451966991726173');
		$app_id = empty($app_id)?'1451966991726173':$app_id;

		$this->assign('url', $url);
		$this->assign('app_id', $app_id);
		$this->assign('width',$this->getConfig('width'));
		$this->assign('title',$this->getConfig('title'));
		$this->assign('height',$this->getConfig('height'));
		$this->assign('theme',$this->getConfig('theme'));
		$this->assign('showfriends',$this->getConfig('enable_showfriends'));
		$this->assign('header',$this->getConfig('enable_header'));
		$this->assign('posts',$this->getConfig('enable_posts'));
		$this->assign('border',$this->getConfig('enable_border'));

		$custom_css = $this->getConfig('custom_css');
		$custom_css = str_replace(" ","+", $custom_css);
		$custom_css = base64_decode($custom_css);
		$custom_css = str_replace(array("\r", "\n"), "", $custom_css);
		$this->assign('css',$custom_css);
		
		return parent::_toHtml();
	}
	
}