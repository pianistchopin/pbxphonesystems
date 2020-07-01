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

class Swiper extends AbstractWidget{
	
	protected $_blockModel;
	protected $_dataFilterHelper;

	protected $_filterProvider;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		$this->_filterProvider = $filterProvider;

		if($this->hasData("template")) {
			$my_template = $this->getData("template");
		}elseif(isset($data['template']) && $data['template']) {
			$my_template = $data['template'];
		}else{
			$my_template = "widget/swiper.phtml";
		}
		$this->setTemplate($my_template);
		
	}

	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$carousels = array();
		$limit = 30;
		$filter = $this->_filterProvider->getPageFilter();

		for($i=1; $i<=$limit; $i++) {
			$tmp = [];
			$tmp['content'] = $this->getConfig("content_".$i);
			$tmp['size'] = $this->getConfig("size_".$i);
			if($tmp['content']) {
				$tmp['content'] = str_replace(" ", "+", $tmp['content']);
				$tmp['content'] = base64_decode($tmp['content']);
				$tmp['content'] = $filter->filter($tmp['content']);
				$carousels[] = $tmp;
			}
		}
		$this->setDataItems($carousels);

		return parent::_toHtml();
	}
}