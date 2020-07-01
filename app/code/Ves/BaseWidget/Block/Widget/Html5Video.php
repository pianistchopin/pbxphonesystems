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
class Html5Video extends AbstractWidget{

	protected $_blockModel;
	protected $_dataFilterHelper;
	protected $_imageHelper;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		\Ves\BaseWidget\Helper\Image $imageHelper,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		$this->_imageHelper = $imageHelper;
		if($template = $this->getConfig('video_template')) {
			$this->setTemplate($template);
		} else {
			$this->setTemplate("widget/html5_video.phtml");
		}
		
	}
	public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		/** THUMBNAIL **/
		$image_file = $this->getData('file');
		$width = (int)$this->getData('width');
		$height = (int)$this->getData('height');
		$thumbnailurl = "";
		if(isset($width) && isset($height) && isset($image_file)){
			$thumbnailurl = $this->_imageHelper->resizeImage($image_file, (int)$width, (int)$height);
		}

		$mp4_file = $this->getData('mp4_file');
		$webm_file = $this->getData('webm_file');
		$ogg_file = $this->getData('ogg_file');

		$subtitles_path = $this->getData('subtitles_path');
		$subtitles_path = $this->getBaseMediaUrl() . $subtitles_path;
		if(!$mp4_file && !$webm_file  && !$ogg_file) return;

		$mp4_file = $this->getBaseMediaUrl() . $mp4_file;
		$webm_file = $this->getBaseMediaUrl() . $webm_file;
		$ogg_file = $this->getBaseMediaUrl() . $ogg_file;

		$swf_file = $this->getBaseMediaUrl() . 'ves_basewidget/video-js.swf';

		$this->assign("image_file", $image_file);
		$this->assign("thumbnailurl", $thumbnailurl);
		$this->assign("width", $width);
		$this->assign("height", $height);
		$this->assign("webm_file", $webm_file);
		$this->assign("mp4_file", $mp4_file);
		$this->assign("ogg_file", $ogg_file);
		$this->assign("subtitles_path", $subtitles_path);
		$this->assign("swf_file", $swf_file);

		return parent::_toHtml();
	}
	
}