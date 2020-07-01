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

class Instagram extends AbstractWidget{
	
	protected $_blockModel;
	protected $_dataFilterHelper;
	/**
	 * @var class Instagram
	 * 
	 * @access protected
	 */
	protected $_instagram = '';

	/**
	 * @var return list
	 * 
	 * @access protected
	 */
	protected $_instagrams = array();

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		\Ves\BaseWidget\Helper\InstagramConnect $instagram,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		$this->_instagram = $instagram;

		if($this->hasData("template")) {
			$my_template = $this->getData("template");
		}else{
			$my_template = "widget/instagram.phtml";
		}
		$this->setTemplate($my_template);
	}

	protected function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;
		$enable_owl_carousel = $this->getConfig("enable_owl_carousel");
		if($enable_owl_carousel) {
			$this->setTemplate("widget/instagram_carousel.phtml");
		}
		
		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('widget_heading', $this->getConfig('title'));
		/*Carousel settings*/
		$this->assign('lazyLoad', $this->getConfig('lazy_load_image'));
		$this->assign('loop', $this->getConfig('loop'));
		$this->assign('rtl', $this->getConfig('rtl'));
		$this->assign('mouse_drag', $this->getConfig('mouse_drag'));
		$this->assign('touch_drag', $this->getConfig('touch_drag'));
		$this->assign('slide_by', $this->getConfig('slide_by'));
		$this->assign('margin_item', $this->getConfig('margin_item'));
		$this->assign('default_items', $this->getConfig('default_items'));
		$this->assign('mobile_items', $this->getConfig('mobile_items'));
		$this->assign('tablet_small_items', $this->getConfig('tablet_small_items'));
		$this->assign('tablet_items', $this->getConfig('tablet_items'));
		$this->assign('portrait_items', $this->getConfig('portrait_items'));
		$this->assign('large_items', $this->getConfig('large_items'));
		$this->assign('custom_items', $this->getConfig('custom_items'));
		$this->assign('auto_play_mode', $this->getConfig('auto_play'));
		$this->assign('interval', $this->getConfig('interval'));
		$this->assign('show_navigator', $this->getConfig('show_navigator'));
		$this->assign('limit', $this->getConfig('limit'));

		$tmpm 	= explode( "x", $this->getConfig('instagram_imagesize'));
		$width 	= (int)$tmpm[0];
		$width 	= empty($width)?200:$width;
		$height = (int)$tmpm[1];
		$height = empty($height)?200:$height;
		$limit 	= (int)$this->getConfig('limit');
		$limit 	= empty($limit)?8:$limit;

		$client_id 			= $this->getConfig('client_id');
		$client_secret 	= $this->getConfig('client_secret');
		$auth_token 	= $this->getConfig('auth_token');
		$hastagname 		= $this->getConfig('hastagname');
		$username 			= $this->getConfig('username');
		$lat 						= $this->getConfig('default_latitude');
		$lng 						= $this->getConfig('default_longitude');
		$distance 			= $this->getConfig('distance');

		$instagram = $this->_instagram->instagram($auth_token);
		$rs_instagram ="";
		if ($this->getConfig('options') == "username") {
			//$user = $instagram->searchUser($username);
			//$user_id = $user->data[0]->id;
			$rs_instagram = $instagram->getUserMedia('self',$limit);
		}elseif ($this->getConfig('options') == "hastag") {
			$rs_instagram = $instagram->getTagMedia($hastagname,$limit);
		}elseif ($this->getConfig('options') == "location") {
		 	$locations 		= $instagram->searchLocation($lat,$lng,$distance);
		 	$location_id 	= (int)$locations->data[0]->id;
		 	$rs_instagram = $instagram->getLocationMedia($location_id);
		}

		if(count($rs_instagram) > 0) {
			foreach($rs_instagram->data as $media) {
				$tmp = array();

				$tmp['link'] 			= $media->link;
				$tmp['image'] 			= $media->images->low_resolution->url;
				$tmp['image_large'] 	= $media->images->standard_resolution->url;
				$tmp['width_large'] 	= $media->images->standard_resolution->width;
				$tmp['height_large'] 	= $media->images->standard_resolution->height;
				$tmp['avatar'] 			= $media->user->profile_picture;
				$tmp['username'] 		= $media->user->username;
				$tmp['comment'] 		= (!empty($media->caption->text)) ? $media->caption->text : '';
				$this->_instagrams[] 	= $tmp;
			}
		}
		// Assgin to phtml
		$this->setWidthinconfig($width);
		$this->setHeightinconfig($height);
		$this->setInstagram($this->_instagrams);
		$this->setInstagramdefault($rs_instagram);

		return parent::_toHtml();
	}

	public function getCmsBlockModel(){
        return $this->_blockModel;
    }

	/**
	  * function Download images from remote server
	  *
	  * @param String inPath
	  * @param String outPath
	  * @return image to local
	*/
	function SaveImage($inPath,$outPath)
	{ //Download images from remote server
		$in=    fopen($inPath, "rb");
		$out=   fopen($outPath, "wb");
		while ($chunk = fread($in,8192))
		{
			fwrite($out, $chunk, 8192);
		}
		fclose($in);
		fclose($out);
	}
	
}