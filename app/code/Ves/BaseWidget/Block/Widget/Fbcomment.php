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

class Fbcomment extends AbstractWidget{

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

		$this->setTemplate("widget/facebook_comment.phtml");
	}
	
	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$use_current_url = $this->getConfig('current_url', 0);
		$url = $this->getConfig('page_url');

		if($use_current_url) {
			$url = $this->actionUrlBuilder->getCurrentUrl();
		}

		$url = str_replace(" ", "+",$url);

		$app_id = $this->getConfig('app_id', '1451966991726173');
		$app_id = empty($app_id)?'1451966991726173':$app_id;
		$this->assign('url', $url);
		$this->assign('app_id', $app_id);
		$this->assign('width',$this->getConfig('width'));
		$this->assign('title',$this->getConfig('title'));
		$this->assign('height',$this->getConfig('height'));
		$this->assign('number_posts',$this->getConfig('number_posts'));
		$this->assign('theme',$this->getConfig('theme'));
		$this->assign('order_by',$this->getConfig('enable_header'));
		
		return parent::_toHtml();
	}
	
}