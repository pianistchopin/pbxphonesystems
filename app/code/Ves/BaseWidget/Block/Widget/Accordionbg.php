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

class Accordionbg extends AbstractWidget
{
	protected $_blockModel;
	protected $_dataFilterHelper;
	protected $_storeManager;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		/*\Magento\Store\Model\StoreManagerInterface $storeManager,*/
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		/*$this->_storeManager = $storeManager;*/

		$my_template = "widget/accordion_bg.phtml";

		if($this->hasData("template") && $this->getData("template")) {
        	$my_template = $this->getData("template");
        }

		$this->setTemplate($my_template);
	}

	public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

	protected function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$accordions = array();
		$limit = 50;

		for($i=1; $i<=$limit; $i++) {
			$tmp = array();
			$tmp['cms'] = $this->getConfig("cms_".$i);
			$tmp['content'] = $this->getConfig("content_".$i);
			$tmp['header'] = base64_decode( $this->getConfig("header_".$i) );
			$tmp['class'] = $this->getConfig("class_".$i);
			$tmp['image'] = $this->getConfig("image_".$i);
			$parsed = parse_url($tmp['image']);
			if (empty($parsed['scheme']) && $tmp['image']) {
				$tmp['image'] = $this->getBaseMediaUrl().$tmp['image'];
			}
			if($tmp['cms']) {
				$tmp['content'] = $this->_blockModel->load($tmp['cms'])->getContent();
		 		$tmp['content'] = $this->_dataFilterHelper->filter($tmp['content']);
			} elseif($tmp['content'] && $tmp['header']) {
				$tmp['content'] = base64_decode($tmp['content']);
				$tmp['content'] = $this->_dataFilterHelper->filter($tmp['content']);

			}

			if($tmp['content'] && $tmp['header'] ) {
				$accordions[] = $tmp;
			}
			
		}

		$this->assign('heading_type', $this->getConfig('heading_type', 3));
		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('accordions', $accordions );
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('widget_heading', $this->getConfig('title'));

		return parent::_toHtml();
	}
}