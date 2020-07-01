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
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Block;

class AbstractWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	protected $_blockModel;
	protected $_dataFilterHelper;
	protected $_layout;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		array $data = []
		) {

		parent::__construct($context, $data);

		$this->_layout = $context->getLayout();
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
	}
	public function getConfig($key, $default = NULL){
		if($this->hasData($key)){
			return $this->getData($key);
		}
		return $default;
	}
	public function getDataFilterHelper() {
		return $this->_dataFilterHelper;
	}
	public function getLayout() {
		return $this->_layout;
	}
	public function getBaseUrl() {
		$_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
		$storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
		$currentStore = $storeManager->getStore();
		
		return $currentStore->getBaseUrl();
	}
	public function isBase64Encoded($data) {
		return $this->_dataFilterHelper->isBase64Encoded($data);
	}
}