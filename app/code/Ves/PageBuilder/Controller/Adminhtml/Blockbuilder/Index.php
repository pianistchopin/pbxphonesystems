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
 * @package    Ves_Brand
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\PageBuilder\Controller\Adminhtml\Blockbuilder;

class Index extends \Ves\PageBuilder\Controller\Adminhtml\Blockbuilder
{

	/**
	 * Brand list action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
	 */
	public function execute()
	{

		$resultPage = $this->resultPageFactory->create();

		/**
		 * Set active menu item
		 */
		$resultPage->setActiveMenu("Ves_PageBuilder::pages_builder");
		$resultPage->getConfig()->getTitle()->prepend(__('Element Builder'));

		/**
		 * Add breadcrumb item
		 */
		$resultPage->addBreadcrumb(__('Pages Builder'),__('Pages Builder'));
		$resultPage->addBreadcrumb(__('Manage Element Builder Profiles'),__('Manage Element Builder Profiles'));

		return $resultPage;
	}
	
}