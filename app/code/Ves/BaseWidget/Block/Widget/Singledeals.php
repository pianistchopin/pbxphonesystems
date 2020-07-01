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
use Ves\BaseWidget\Block\AbstractProductWidget;
use Magento\Framework\Url;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Singledeals extends AbstractProductWidget{
	/** @var UrlBuilder */
    protected $actionUrlBuilder;


    protected $_objectManager;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Cms\Model\Block $blockModel,
        \Ves\BaseWidget\Helper\Data $dataHelper,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Url $actionUrlBuilder,
        UrlFinderInterface $urlFinder,
        array $data = []
        ) {
        parent::__construct($context, $urlHelper, $blockModel, $dataHelper, $productRepository, $urlFinder, $data);
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->_objectManager = $objectManager;
        if($this->hasData("template")) {
            $my_template = $this->getData("template");
        } elseif(isset($data['template']) && $data['template']) {
            $my_template = $data['template'];
        } else{
            $my_template = "widget/singledeals.phtml";
        }
        
        $this->setTemplate($my_template);
    }

    public function getMediaUrl(){
        $storeMediaUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
        ->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $storeMediaUrl;
    }
}