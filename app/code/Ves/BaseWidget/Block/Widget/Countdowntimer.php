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

class Countdowntimer extends AbstractWidget{
    /** @var UrlBuilder */
    protected $actionUrlBuilder;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    protected $_imageHelper;

    protected $_localeDate;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Block $blockModel,
        \Ves\BaseWidget\Helper\Data $dataHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ves\BaseWidget\Helper\Image $imageHelper,
        array $data = []
        ) {

        parent::__construct($context, $blockModel, $dataHelper);
        $this->_objectManager = $objectManager;
        $this->_imageHelper = $imageHelper;
        $this->_localeDate = $context->getLocaleDate();

        if($this->hasData("template")) {
            $my_template = $this->getData("template");
        } elseif(isset($data['template']) && $data['template']) {
            $my_template = $data['template'];
        } else{
            $my_template = "widget/countdowntimer.phtml";
        }
        $this->setTemplate($my_template);
    }

    public function getLocaleDate() {
        return $this->_localeDate;
    }
    public function getImageHelper() {
        return $this->_imageHelper;
    }
    public function filter($str)
    {
        $html = $this->getDataFilterHelper()->filter($str);
        return $html;
    }
    
    public function getBaseMediaUrl(){
        $storeMediaUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
        ->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $storeMediaUrl;
    }
}