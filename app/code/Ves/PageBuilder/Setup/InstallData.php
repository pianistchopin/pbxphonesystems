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
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\PageBuilder\Setup;

use Ves\PageBuilder\Model\Block;
use Ves\PageBuilder\Model\BlockFactory;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
	/**
	 * PageBuilder Factory
	 *
	 * @var PageBuilderFactory
	 */
	private $pagebuilderFactory;

	/**
	 * @param PageBuilderFactory $brandFactory 
	 * @param GroupFactory $groupFactory 
	 */
	public function __construct(
		BlockFactory $pagebuilderFactory
		)
	{
		$this->pagebuilderFactory = $pagebuilderFactory;
	}

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		
	}
	
}