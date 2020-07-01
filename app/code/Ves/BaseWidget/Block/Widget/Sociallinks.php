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

class Sociallinks extends AbstractWidget{

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
			$my_template = "widget/sociallinks.phtml";
		}
		$this->setTemplate($my_template);
	}

	protected function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;
		
		if($data = $this->getData()) {
			foreach($data as $key=>$val) {
				$this->assign($key, $this->getConfig($key));
			}
		}
		$this->assign('widget_heading', $this->getConfig('title'));	
		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('facebook_link', $this->getConfig('facebook_link'));	
		$this->assign('twitter_link', $this->getConfig('twitter_link'));
		$this->assign('pinterest_link', $this->getConfig('pinterest_link'));
		$this->assign('google_plus', $this->getConfig('google_plus'));
		$this->assign('youtube', $this->getConfig('youtube'));
		$this->assign('skype', $this->getConfig('skype'));
		$this->assign('vimeo', $this->getConfig('vimeo'));
		$this->assign('instagram', $this->getConfig('instagram'));
		$this->assign('linkedin', $this->getConfig('linkedin'));
		return parent::_toHtml();
	}
	
}