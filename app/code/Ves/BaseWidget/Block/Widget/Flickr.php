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

class Flickr extends AbstractWidget{
	
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
		$this->setTemplate("widget/flickr.phtml");
	}

	protected function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$this->assign('userId',$this->getConfig('user_id'));
		$this->assign('speed',$this->getConfig('speed'));
		$this->assign('title',$this->getConfig('title'));
		$this->assign('thumbnail',$this->getConfig('thumbnail'));
		$this->assign('popup',$this->getConfig('popup'));
		$this->assign('popupImageWidth',$this->getConfig('popup_image_width'));
		$this->assign('popupImageHeight',$this->getConfig('popup_image_height'));
		$this->assign('thumbnailImageWidth',$this->getConfig('thumbnail_image_width'));
		$this->assign('thumbnailImageHeight',$this->getConfig('thumbnail_image_height'));


		$params = array(
			'api_key'	=> $this->getConfig('api_id'),
			'method'	=> 'flickr.people.getPhotos',
			'user_id'   => $this->getConfig('user_id'),
			'per_page'  => $this->getConfig('number_show'),
			'format'	=> 'php_serial',
			);

		$encoded_params = array();

		foreach ($params as $k => $v){

			$encoded_params[] = urlencode($k).'='.urlencode($v);
		}
		try{
			$url = "https://api.flickr.com/services/rest/?".implode('&', $encoded_params);

			$rsp = file_get_contents($url);

			$rsp_obj = unserialize($rsp);

			if ($rsp_obj['stat'] == 'ok'){

				$this->assign('photos',$rsp_obj['photos']['photo']);

			}
		 }catch (\Exception $e) {
			throw new \Exception(__('Disconnect'));
		}
		return parent::_toHtml();
	}
	
}

