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

class Googlemap extends AbstractWidget{

	protected $_blockModel;
	protected $_dataFilterHelper;
	protected $_storeManager;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		/*\Magento\Store\Model\StoreManagerInterface $storeManager,*/
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		/*$this->_storeManager = $storeManager;*/

		$this->setTemplate("widget/map.phtml");
	}

	public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

	protected function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$image_file = $this->getConfig('maker_icon');
		$imageurl = $image_file;
		if($image_file && !preg_match("/^http\:\/\/|https\:\/\//", $image_file)) {
            $imageurl = $this->getBaseMediaUrl() . $image_file;
        }

        $this->assign('maker_icon', $imageurl);
        $this->assign('google_api', $this->getConfig('google_api'));
		$this->assign('description', $this->getConfig('description'));
        $this->assign('latitude', $this->getConfig('latitude'));
		$this->assign('longitude', $this->getConfig('longitude'));
		$this->assign('zoom', $this->getConfig('zoom'));
		$this->assign('width', $this->getConfig('width'));
		$this->assign('height', $this->getConfig('height'));
		$this->assign('is_preview', 1);
		return parent::_toHtml();
	}
	
}