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
 * @package    Ves_ImageSlider
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Block\Widget;
use Ves\BaseWidget\Block\AbstractWidget;

class Image extends AbstractWidget
{
	protected $_storeManager;
	protected $_blockModel;
	protected $_dataFilterHelper;
	protected $_imageHelper;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		/*\Magento\Store\Model\StoreManagerInterface $storeManager,*/
		\Ves\BaseWidget\Helper\Data $dataHelper,
		\Ves\BaseWidget\Helper\Image $imageHelper,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		$this->_imageHelper = $imageHelper;
		/*$this->_storeManager = $storeManager;*/
		$this->setTemplate('widget/single_image.phtml');
	}

	public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getImageSize( $imagesize = "") {
    	$imagesize = trim($imagesize);
    	$return = "";
    	switch ($imagesize) {
    		case 'full':
    			# code...
    			$return = "0x0";
    			break;
    		case 'large':
    			# code...
    			$return = "1200x1200";
    			break;
    		case 'medium':
    			$return = "900x800";
    			break;
    		case 'thumbnail':
    		case '':
    			$return = "300x300";
    			break;
    		default:
    			$return = $imagesize;
    			break;
    	}
    	return $return;
    }

	public function _toHtml() {
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$widget_heading = $this->getConfig("title");
		$image_file = $this->getConfig('file');
		$imagesize = $this->getConfig('image_size');

		$imagesize = $this->getImageSize( $imagesize );

		$imageurl = "";
		
		if(!preg_match("/^http\:\/\/|https\:\/\//", $image_file)) {
            $imageurl = $this->getBaseMediaUrl() . $image_file;
        }

		$array_size = explode("x", $imagesize);
		$image_width = isset($array_size[0])?(int)$array_size[0]:0;
		$image_width = $image_width?$image_width: 0;
		$image_height = isset($array_size[1])?(int)$array_size[1]:0;
		$image_height = $image_height?$image_height: 0;

		$thumbnailurl = "";
		if ($image_file && !preg_match("/^http\:\/\/|https\:\/\//", $image_file)) {
            $thumbnailurl = $this->_imageHelper->resizeImage($image_file, (int)$image_width, (int)$image_height);
        } else {
        	$thumbnailurl = $imageurl = $image_file;
        }
        /*Use holder image*/
        if ($image_file && preg_match("/^holder.js/", $image_file)) {
        	$thumbnailurl = $imageurl = $image_file;
        }

        $link_url = $this->getConfig('link');

        if ($link_url && !preg_match("/^http\:\/\/|https\:\/\//", $link_url)) {
        	$link_url = str_replace(" ", "+", $link_url);
        	if($this->isBase64Encoded($link_url)){
				$link_url = base64_decode($link_url);
				if($link_url) {
					$link_url = $this->getDataFilterHelper()->filter($link_url);
				}
			}
        }

        $this->assign('image_width', $image_width);
        $this->assign('image_height', $image_height);
        $this->assign('widget_heading', $widget_heading);
		$this->assign('imageurl', $imageurl);
		$this->assign('thumbnailurl', $thumbnailurl);
		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('animation', $this->getConfig('animation'));
		$this->assign('alignment', $this->getConfig('alignment'));
		$this->assign('ispopup',$this->getConfig('popup'));
		$this->assign('link_url',$link_url);
		$this->assign('alt',$this->getConfig('alt'));
		$this->assign('zoom_type', $this->getConfig('zoom_type'));
		$this->assign('lens_size', $this->getConfig('lens_size', 200));
		$this->assign('image_style', $this->getConfig('image_style',''));

		return parent::_toHtml();
	}

}