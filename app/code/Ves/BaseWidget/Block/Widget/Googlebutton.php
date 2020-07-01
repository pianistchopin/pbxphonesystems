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

class Googlebutton extends AbstractWidget{

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
		$this->setTemplate("widget/google_button.phtml");
	}

	protected function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;
		
        $this->assign('widget_heading', $this->getConfig('title'));
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('width', $this->getConfig('width'));
		$this->assign('annotation', $this->getConfig('annotation'));
		$this->assign('size', $this->getConfig('button_size'));
		return parent::_toHtml();
	}
	
}