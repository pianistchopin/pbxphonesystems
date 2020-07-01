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
 * @package    Ves_Baseconnector
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Controller\Adminhtml\Basewidget;
use Magento\Framework\App\Filesystem\DirectoryList;

class Imagemanager extends \Ves\BaseWidget\Controller\Adminhtml\Basewidget {

    /**
     * index action
     */ 
    public function execute() {
        $resultPage = $this->resultPageFactory->create();

        /**
         * Set active menu item
         */
        $resultPage->setActiveMenu("Ves_PageBuilder::pages_builder");
        $resultPage->getConfig()->getTitle()->prepend(__('Images Manager - Ves Base Widget'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('Ves Base Widget'),__('Ves Base Widget'));
        $resultPage->addBreadcrumb(__('Images Manager'),__('Images Manager'));

        return $resultPage;
    }
    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ves_BaseWidget::manage_media');
    }
    
}
?>