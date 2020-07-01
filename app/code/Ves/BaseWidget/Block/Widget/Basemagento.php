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

class Basemagento extends AbstractWidget
{

	protected $_blockModel;
	protected $_dataFilterHelper;
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
	}

	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$core_block = $this->getMagentoBlock();
		$custom_template = $this->getConfig("custom_template", "");
		if($core_block) {
			if($custom_template) {
				$core_block->setTemplate($custom_template);
			}
			return $core_block->toHtml();
		}
		return;
	}

	protected function _prepareLayout() {
        $block_name = $this->getConfig("block_name", "");
        $custom_block_name = $this->getConfig("custom_block_name", "");
        if($custom_block_name) {
        	$block_name = $custom_block_name;
        }
		$core_block = null;
		if($block_name) {
			$core_block = $this->getLayout()->getBlock($block_name);
		}
		$this->setMagentoBlock($core_block);

        return parent::_prepareLayout();
    }
	
}