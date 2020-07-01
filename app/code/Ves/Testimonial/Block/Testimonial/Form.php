<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Testimonial
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Testimonial\Block\Testimonial;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ves\Testimonial\Helper\Data
     */
    protected $_testimonialData;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface 
     */
    protected $scopeConfig;

    /**
    * @var \Ves\Testimonial\Model\Testimonial
    **/
    protected $_testimonialCollection;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context     
     * @param \Magento\Framework\Registry                      $registry    
     * @param \Ves\Blog\Model\Post                             $postFactory 
     * @param \Ves\Blog\Helper\Data                            $blogHelper  
     * @param array                                            $data        
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ves\Testimonial\Helper\Data $testimonialData,
        // \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ves\Testimonial\Model\Testimonial $testimonialCollection,
        array $data = []
        ) {
        $this->_testimonialData = $testimonialData;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_testimonialCollection = $testimonialCollection;
        parent::__construct($context, $data);

    }

     protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Create New Testimonial'));
        parent::_prepareLayout();
    }

    function getMediaBaseUrl() {
        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
        $storeManager = $om->get('Magento\Store\Model\StoreManagerInterface');
        /** @var \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Model\Store $currentStore */
        $currentStore = $storeManager->getStore();
        return $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    function getRatingHtml(){
        return $this->_testimonialData->getStarRating();
    }
}