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
 * @package    Ves_PageBuilder
 * @copyright  Copyright (c) 2017 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\PageBuilder\Controller\Preview;

use Magento\Backend\App\Action;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Ves\PageBuilder\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Action\Context      $context           
     * @param \Magento\Framework\App\ResourceConnection  $resource          
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager      
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory 
     * @param \Ves\PageBuilder\Helper\Data                $dataHelper            
     * @param \Magento\Framework\Registry                $registry          
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ves\PageBuilder\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry
    ) {
    	parent::__construct($context);
        $this->resultPageFactory  = $resultPageFactory;
        $this->_coreRegistry      = $registry;
        $this->_resource          = $resource;
        $this->_dataHelper        = $dataHelper;
        $this->_storeManager      = $storeManager;
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $page   = $this->resultPageFactory->create();
        $block_id = $this->getRequest()->getParam("block_id");
        $model  = $this->_objectManager->create('Ves\PageBuilder\Model\Block');

        if ($block_id) {
            $model->load($block_id);
            $this->_coreRegistry->register('current_blockprofile', $model);
        }
        return $page;
    }
}