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

class Accordion extends AbstractWidget
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

		$my_template = "widget/accordion.phtml";

		if($this->hasData("layout_type") && ($layout_type = $this->getData("layout_type"))) {
			$my_template = $layout_type;
		}
		if($this->hasData("template") && $this->getData("template")) {
        	$my_template = $this->getData("template");
        }

		$this->setTemplate($my_template);
	}

	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$accordions = array();
		$limit = 50;
		for($i=1; $i<=$limit; $i++) {
			$tmp = array();
			$tmp['cms'] = $this->getConfig("cms_".$i);
			$tmp['content'] = $this->getConfig("content_".$i);
			$tmp['header'] = $this->getConfig("header_".$i);
			if($tmp['cms']) {
		 		$tmp['content'] = $this->_blockModel->load($tmp['cms'])->getContent();
		 		$tmp['content'] = $this->_dataFilterHelper->filter($tmp['content']);
			} elseif($tmp['content'] && $tmp['header']) {
				$tmp['content'] = str_replace(" ", "+", $tmp['content']);
				$tmp['content'] = base64_decode($tmp['content']);
				$tmp['content'] = $this->_dataFilterHelper->filter($tmp['content']);

			}
			if($tmp['content'] && $tmp['header'] ) {
				$accordions[] = $tmp;
			}
			
		}
		$active = $this->getConfig('active', "0");
		$active = trim($active);
		$active = $active?$active:'0';

		$this->assign('open_on_focus', $this->getConfig('open_on_focus', 0));
		$this->assign('multi', $this->getConfig('multi', 0));
		$this->assign('active', $active);
		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('accordions', $accordions );
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('widget_heading', $this->getConfig('title'));

		return parent::_toHtml();
	}
}