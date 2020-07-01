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

class Twitter extends AbstractWidget{

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
		
		$this->setTemplate("widget/twitter.phtml");
	}

	
	protected function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;
		$this->assign('id',$this->getConfig('id'));
		$this->assign('title',$this->getConfig('title'));
		$this->assign('username',$this->getConfig('username'));
		$this->assign('width',$this->getConfig('width'));
		$this->assign('height',$this->getConfig('height'));
		$this->assign('theme',$this->getConfig('theme'));

		$scrollbar = $this->getConfig('enable_scrollbar');
		$scrollbar = !$scrollbar?'noscrollbar':'';
		$this->assign('scrollbar', $scrollbar);

		$enable_header = $this->getConfig('enable_header');
		$enable_header = !$enable_header?'noheader':'';
		$this->assign('header',$enable_header);

		$enable_footer = $this->getConfig('enable_footer');
		$enable_footer = !$enable_footer?'nofooter':'';
		$this->assign('footer',$enable_footer);
		
		$enable_border = $this->getConfig('enable_border');
		$enable_border = !$enable_border?'noborders':'';
		$this->assign('border',$enable_border);
		
		$this->assign('borderColor',$this->getConfig('border_color'));
		$this->assign('limit',$this->getConfig('limit'));
		$this->assign('linkColor',$this->getConfig('link_color'));
		return parent::_toHtml();
	}
	
}