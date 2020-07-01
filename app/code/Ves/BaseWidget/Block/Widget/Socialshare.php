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

class Socialshare extends AbstractWidget{

	protected $_blockModel;
	protected $_dataFilterHelper;
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		\Magento\Framework\Registry $registry,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		$this->_coreRegistry = $registry;

		if($this->hasData("template")) {
			$my_template = $this->getData("template");
		}else{
			$my_template = "widget/socialshare.phtml";
		}
		$this->setTemplate($my_template);
	}

	public function getStoreName()
	{
	    return $this->_dataFilterHelper->getConfig(
	        'store_information/name',
	        '',
	        'general'
	    );
	}


	protected function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$this->assign('widget_heading', $this->getConfig('title'));	
		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('stylecls', $this->getConfig('stylecls'));
		return parent::_toHtml();
	}

	public function getProduct() {
		return $this->_coreRegistry->registry('product');
	}

}