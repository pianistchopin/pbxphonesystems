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

class Heading extends AbstractWidget{

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
 			$my_template = "widget/heading.phtml";
 		}
 		$this->setTemplate($my_template);
	}

	protected function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$content_html = $this->getConfig('content');
		$content_html = str_replace(" ", "+", $content_html);
		if(is_string($content_html) && $this->isBase64Encoded($content_html)) {
			$content_html = base64_decode($content_html);
		}
		
		if($content_html) {
			$content_html = $this->getDataFilterHelper()->filter($content_html);
		}
		
		$this->assign('content', $content_html);
		$this->assign('heading_tag', $this->getConfig('heading_tag', 'h1'));
		$this->assign('prefix', $this->getConfig('prefix'));

		return parent::_toHtml();
	}

}