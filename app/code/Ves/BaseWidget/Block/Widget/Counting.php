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
class Counting extends AbstractWidget{

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
        	$my_template = $this->getConfig("template");
        }else{
 			$my_template = "widget/counting_number.phtml";
 		}

		$this->setTemplate($my_template);
	}

	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$description = $this->getConfig('html');
		$description = str_replace(" ", "+", $description);
		$description = base64_decode($description);

		if($description && $this->getConfig("show_description", 0)) {
			$description = $this->getDataFilterHelper()->filter($description);
		} else {
			$description = "";
		}

		$speed = $this->getConfig("speed", 8000);

		$this->assign("speed", $speed);
		$this->assign('description', $description);	
		$this->assign('title', $this->getConfig('title'));
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('icon', $this->getConfig('icon'));
		$this->assign('number', $this->getConfig('number'));
		$this->assign('font_size', $this->getConfig('font_size'));

		return parent::_toHtml();
	}
}