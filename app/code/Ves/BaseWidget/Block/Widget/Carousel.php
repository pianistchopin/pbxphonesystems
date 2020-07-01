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

class Carousel extends AbstractWidget
{

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

		if($template = $this->getConfig("template")) {
			$this->setTemplate($template);
		} else {
			$this->setTemplate("widget/carousel.phtml");
		}
		
	}

	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$carousels = array();
		$limit = 50;

		for($i=1; $i<=$limit; $i++) {
			$tmp = array();
			$tmp['cms'] = $this->getConfig("cms_".$i);
			$tmp['content'] = $this->getConfig("content_".$i);
			$tmp['header'] = $this->getConfig("header_".$i);
			
			if($tmp['cms']) {
				$tmp['content'] = $this->_blockModel->load($tmp['cms'])->getContent();
		 		$tmp['content'] = $this->_dataFilterHelper->filter($tmp['content']);
			} elseif($tmp['content']) {
				$tmp['content'] = str_replace(" ", "+", $tmp['content']);
				$tmp['content'] = base64_decode($tmp['content']);
				$tmp['content'] = $this->getDataFilterHelper()->filter($this->_dataFilterHelper->decodeImg($tmp['content']));
			}
			if($tmp['content'] || $tmp['header'] ) {
				$carousels[] = $tmp;
			}
		}

		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('carousels', $carousels );
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('widget_heading', $this->getConfig('title'));
		/*Carousel settings*/
		$this->assign('loop', $this->getConfig('loop'));
		$this->assign('mouse_drag', $this->getConfig('mouse_drag'));
		$this->assign('touch_drag', $this->getConfig('touch_drag'));
		$this->assign('slide_by', $this->getConfig('slide_by'));
		$this->assign('margin_item', $this->getConfig('margin_item'));
		$this->assign('default_items', $this->getConfig('default_items'));
		$this->assign('mobile_items', $this->getConfig('mobile_items'));
		$this->assign('tablet_small_items', $this->getConfig('tablet_small_items'));
		$this->assign('tablet_items', $this->getConfig('tablet_items'));
		$this->assign('portrait_items', $this->getConfig('portrait_items'));
		$this->assign('large_items', $this->getConfig('large_items'));
		$this->assign('custom_items', $this->getConfig('custom_items'));
		$this->assign('auto_play_mode', $this->getConfig('auto_play'));
		$this->assign('interval', $this->getConfig('interval'));
		$this->assign('show_nav', $this->getConfig('show_navigator'));

		return parent::_toHtml();
	}
	
}