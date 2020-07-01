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
use Magento\Framework\Url;
use Magento\Framework\UrlInterface;

class Links extends AbstractWidget{

	protected $_blockModel;
	protected $_dataFilterHelper;
	/** @var UrlBuilder */
    protected $actionUrlBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		Url $actionUrlBuilder,
        /*UrlInterface $urlBuilder,*/
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		/*$this->urlBuilder = $urlBuilder;*/
        $this->actionUrlBuilder = $actionUrlBuilder;

		if($this->getConfig("enable_collapse")) {
			$this->setTemplate( "widget/accordion_links.phtml" );
		} else {
			$this->setTemplate("widget/links.phtml");
		}
		
	}

	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$widget_heading = $this->getConfig("title");
		$limit_links = 50;
		$links = array();
		for($i=1; $i<=$limit_links; $i++) {
			$tmp = array();
			$tmp['link'] = $this->getConfig("link_".$i);
			$parsed = parse_url($tmp['link']);
			if (empty($parsed['scheme']) && $tmp['link'] != "#") {
				$tmp['link'] = $this->actionUrlBuilder->getDirectUrl( $tmp['link'] );
		    }
			$tmp['target'] = $this->getConfig("link_target_".$i);
			$tmp['link_icon'] = $this->getConfig("link_icon_".$i);
			$tmp['text'] = $this->getConfig("text_link_".$i);
			if($tmp['link'] && $tmp['text']) {
				$links[] = $tmp;
			}
		}
        $this->assign('widget_heading', $widget_heading);
		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('ul_cls', $this->getConfig('ul_cls'));
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('links', $links);

		return parent::_toHtml();
	}
	
}