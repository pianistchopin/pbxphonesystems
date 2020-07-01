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

class Pricing extends AbstractWidget{

	protected $_blockModel;
	protected $_dataFilterHelper;
	protected $_localeCurrency;
	protected $_directoryHelper;
	/** @var UrlBuilder */
    protected $actionUrlBuilder;
    protected $_productModel;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		\Magento\Directory\Helper\Data $directoryHelper,
		\Magento\Framework\Locale\CurrencyInterface $localeCurrency,
		\Magento\Catalog\Model\Product $productModel,
		Url $actionUrlBuilder,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		$this->_localeCurrency = $localeCurrency;
		$this->_directoryHelper = $directoryHelper;
		$this->_productModel = $productModel;
		$this->actionUrlBuilder = $actionUrlBuilder;

		if($this->hasData("template")) {
        	$my_template = $this->getData("template");
        }else{
 			$my_template = "widget/pricing.phtml";
 		}
        $this->setTemplate($my_template);
	}

	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$content = $this->getConfig('content');
		$content = str_replace(" ", "+", $content);
		$content = base64_decode($content);

		if($content) {
			$content = $this->getDataFilterHelper()->filter($content);
		}

		$this->assign('widget_heading', $this->getConfig('title'));	
		$this->assign('class', $this->getConfig('addition_cls'));
		$this->assign('stylecls', $this->getConfig('stylecls'));

		$currency = $this->_localeCurrency->getCurrency($this->_directoryHelper->getBaseCurrencyCode())->getSymbol();

		$price = $this->getConfig('price');
		$link = $this->getConfig('link');
		if ($link && !preg_match("/^http\:\/\/|https\:\/\//", $link) && $link != "#") {
			$link = str_replace(" ", "+", $link);
			$link = base64_decode($link);
			if($link) {
				$link = $this->getDataFilterHelper()->filter($link);
			}
	    }

		if($this->getConfig("use_product_info")) { //If use product information
			$idPath = explode('/', $this->getConfig('id_path'));
	        if (isset($idPath[1])) {
	            $id = $idPath[1];
	            if ($id) {
	                $_product = $this->_productModel->load($id);
	                if($_product) {
	                	$link = $_product->getUrlModel()->getUrl($_product, []);
	                	$price = $_product->getFinalPrice();
	                }
	            }
	        }
		}

		$this->assign('subtitle', $this->getConfig('subtitle'));	
		$this->assign('currency', $currency);
		$this->assign('price', $price);	
		$this->assign('period', $this->getConfig('period'));
		$this->assign('linktitle', $this->getConfig('linktitle'));	
		$this->assign('link', $link);
		$this->assign('isfeatured', $this->getConfig('isfeatured'));
		$this->assign('content', $content);

		return parent::_toHtml();
	}
	
}