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
use Magento\Framework\Url;
use Magento\Framework\UrlInterface;


class Ourservice extends AbstractWidget{


	protected $_blockModel;
	protected $_dataFilterHelper;
	/** @var UrlBuilder */
    protected $actionUrlBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;
    protected $_storeManager;
    protected $_imageHelper;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		/*\Magento\Store\Model\StoreManagerInterface $storeManager,*/
		\Ves\BaseWidget\Helper\Data $dataHelper,
		\Ves\BaseWidget\Helper\Image $imageHelper,
		Url $actionUrlBuilder,
        /*UrlInterface $urlBuilder,*/
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		/*$this->urlBuilder = $urlBuilder;*/
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->_imageHelper = $imageHelper;
		/*$this->_storeManager = $storeManager;*/

        if($this->getConfig("template")) {
			$this->setTemplate($this->getConfig("template"));
		}else{
			$this->setTemplate("widget/ourservice.phtml");
		}
		
	}
	public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

	protected function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$content_html = $this->getConfig('content_html');
		$content_html = str_replace(" ", "+", $content_html);
		$content_html = base64_decode($content_html);
		if($content_html) {
			$content_html = $this->getDataFilterHelper()->filter($content_html);
		}

		$this->assign('wrapper_class', $this->getConfig('wrapper_class'));
		$this->assign('inner_class', $this->getConfig('inner_class'));
		$this->assign('widget_heading', $this->getConfig('title'));
		$this->assign('widget_heading_class', $this->getConfig('title_class'));
		$this->assign('content', $content_html);
		$this->assign('content_class', $this->getConfig('contentclass'));
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('icon_class', $this->getConfig('icon_class'));
		$this->assign('image_class', $this->getConfig('image_class'));

		$image_file = $this->getConfig('file');

		$imagesize = $this->getConfig('imagesize');
		$array_size = explode("x", $imagesize);
		$image_width = isset($array_size[0])?(int)$array_size[0]:0;
		$image_width = $image_width?$image_width: 0;
		$image_height = isset($array_size[1])?(int)$array_size[1]:0;
		$image_height = $image_height?$image_height: 0;

		$thumbnailurl = "";

		if ($image_file && !preg_match("/^http\:\/\/|https\:\/\//", $image_file)) {
            $thumbnailurl = $this->_imageHelper->resizeImage($image_file, (int)$image_width, (int)$image_height);
            $imageurl = $this->getBaseMediaUrl() . $image_file;
        } else {
        	$thumbnailurl = $imageurl = $image_file;
        }
        $link = $this->getConfig("link", "");
		if ($link && !preg_match("/^http\:\/\/|https\:\/\//", $link) && $link != "#") {
			$link = str_replace(" ", "+", $link);
			$link = base64_decode($link);
			if($link) {
				$link = $this->getDataFilterHelper()->filter($link);
			}
	    }

		$font_size = $this->getConfig('font_size','');

		$this->assign('link', $link);
		$this->assign('image_width', (int)$image_width);
		$this->assign('image_height', (int)$image_height);
		$this->assign('font_size', $font_size);
		$this->assign('thumbnailurl', $thumbnailurl);
		$this->assign('imageurl', $imageurl);
		$this->assign('icon_position', $this->getConfig('icon_position'));


		return parent::_toHtml();
	}
}