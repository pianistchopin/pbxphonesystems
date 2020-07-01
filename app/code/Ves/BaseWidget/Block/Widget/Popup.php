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

class Popup extends AbstractWidget{

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
			$my_template = "widget/form_popup.phtml";
		}
		$this->setTemplate($my_template);

	}

	public function _toHtml(){

		$form_widget_type = $this->getConfig('form_type');

		$form_template = '';
		$form_type = '';
		switch ($form_widget_type) {
			case 'newsletter':
			$form_type = 'Magento\Newsletter\Block\Subscribe';
			$form_template = 'subscribe.phtml';
			break;
			case 'login':
			$form_type = 'Magento\Customer\Block\Form\Login';
			$form_template = 'form/login.phtml';
			break;
			case 'register':
			$form_type = 'Magento\Customer\Block\Form\Register';
			$form_template = 'form/register.phtml';
			break;
			case 'forgotpassword':
			$form_type = 'Magento\Customer\Block\Account\Forgotpassword';
			$form_template = 'form/forgotpassword.phtml';
			break;
		}
		$html = '';

		if(trim($this->hasData('form_template')) != ''){
			$form_template = $this->getConfig('form_template');
		}
		if($form_template){
			$html = $this->getLayout()->createBlock($form_type)->setTemplate($form_template)->toHtml();
		}else{
			$cms = $this->getConfig("cms", 0);
			if($form_widget_type == "cms" && $cms) {
				$customHtml = $this->_blockModel->load($cms)->getContent();
			} else {
				$html = $this->getConfig('html');
				$html = str_replace(" ", "+", $html);
				if (base64_decode($html, true) == true){
					$customHtml = html_entity_decode(base64_decode($html));
				}else{
					$customHtml = $this->getConfig('html');
				}
			}
			
			$customHtml = $this->getDataFilterHelper()->filter($customHtml);
			$html = $customHtml;
		}

		$this->assign('html',$html);
		return parent::_toHtml();
	}

}