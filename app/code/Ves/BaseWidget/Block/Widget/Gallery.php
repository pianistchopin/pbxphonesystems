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
use Magento\Framework\App\Filesystem\DirectoryList;

class Gallery extends AbstractWidget
{
	
	protected $_blockModel;
	protected $_dataFilterHelper;
	protected $_imageHelper;
	protected $_storeManager;
	//protected $_filesystem;

	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\BaseWidget\Helper\Data $dataHelper,
		/*\Magento\Store\Model\StoreManagerInterface $storeManager,*/
		\Ves\BaseWidget\Helper\Image $imageHelper,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		$this->_imageHelper = $imageHelper;
		$this->_objectManager = $objectManager;
		//$this->_filesystem = $context->getFilesystem();
		/*$this->_storeManager = $storeManager;*/

		if($this->hasData("template")) {
			$my_template = $this->getData("template");
		} elseif($this->hasData("custom_template")) {
			$my_template = $this->getData("custom_template");
		}else{
			$my_template = "widget/gallery_list.phtml";
		}

		$this->setTemplate($my_template);
	}

	public function getMediaUrl(){
        $storeMediaUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
        ->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $storeMediaUrl;
    }

    public function getBaseMediaDirPath() {
        return $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
    }


	public function _toHtml() {
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$widget_heading = $this->getConfig("title");
		
		$galleries = array();
		$limit = $this->getConfig("limit_item", 10);
		$keep_ratio = $this->getConfig("keep_ratio", 1);;

		if($this->getConfig("source") == "folder") { //If source gallery is folder images

			$folder = $this->getConfig("image_folder","gallery/upload");
			$path = str_replace( DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR, $this->getBaseMediaDirPath() . DIRECTORY_SEPARATOR . str_replace("/",DIRECTORY_SEPARATOR, $folder ));		
			$files = array();
			
			if( is_dir($path) ){ 
				$files = $this->dirFiles( $path );
			}

			$mediaURL = $this->getBaseMediaUrl();
			//$this->thumbdir = 
			$count = 1;
			if( $files ){
				foreach( $files as $file ){
					if($count <= $limit) {
						$tmp 					= array();
						$tmp['title'] 			= $file;
						$tmp['imageURL'] 		= $mediaURL. str_replace(DIRECTORY_SEPARATOR,"/",$folder)."/".$file;
						$tmp['thumbnailURL'] 	= $this->_imageHelper->resizeImage($folder."/".$file, (int)$this->getConfig("thumb_width",200), (int)$this->getConfig("thumb_height",200), 100, $keep_ratio);;
						$tmp['description'] 	= "";
						$tmp['link'] 			= "";

						if(!$this->getConfig('popup') && $tmp['link']) {
							$tmp['imageURL'] = $tmp['link'];
						}

						$galleries[] 			= $tmp;
					} else {
						break;
					}
					$count++;
				}
			}

		} else { //If source gallery is file banner

			for($i=1; $i<=$limit; $i++) {
				$tmp = array();
				$tmp['link'] = $this->getConfig("link_".$i);
				$tmp['title'] = $this->getConfig("title_".$i);
				$tmp['title'] = trim($tmp['title']);
				$tmp['product_id'] = $this->getConfig("product_id_".$i);
				$image_file = $this->getConfig("image_".$i);
				$imageurl = "";

				if($image_file) {
					if(!preg_match("/^http\:\/\/|https\:\/\//", $image_file)) {
						$imageurl = $this->getBaseMediaUrl() . $image_file;
					}

					$thumbnailurl = "";
					if ($image_file && !preg_match("/^http\:\/\/|https\:\/\//", $image_file)) {
						$thumbnailurl = $this->_imageHelper->resizeImage($image_file, (int)$this->getConfig("thumb_width",200), (int)$this->getConfig("thumb_height",200), 100, $keep_ratio);
					} else {
						$thumbnailurl = $imageurl = $image_file;
					}
					/*Use holder image*/
					if ($image_file && preg_match("/^holder.js/", $image_file)) {
						$thumbnailurl = $imageurl = $image_file;
					}
					$tmp['imageURL'] = $imageurl;
					if(!$this->getConfig('popup') && $tmp['link']) {
						$tmp['imageURL'] = $tmp['link'];
					}
					$tmp['thumbnailURL'] 	= $thumbnailurl;
					$tmp['products']	 	= array();
					$tmp['description']		= "";
					if($tmp['product_id']) {
						$arr = explode(',', $tmp['product_id']);
						if($arr){
							$tmp['products'] = $this->getListProducts($arr);//Get collection products by ids
						}
					}

					$galleries[] = $tmp;
				}
			}

		}

		$this->assign('widget_heading', $widget_heading);
		$this->assign('images', $galleries);
		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('ispopup',$this->getConfig('popup'));
		$this->assign('popup_plugin',$this->getConfig('popup_plugin', "colorbox"));
		$this->assign('enable_thumb',$this->getConfig('enable_thumb'));
		$this->assign('use_custom_button',$this->getConfig('use_custom_button'));
		$this->assign('popup_thumb_width',$this->getConfig('popup_thumb_width', 50));
		$this->assign('popup_thumb_height',$this->getConfig('popup_thumb_height', 50));
		
		return parent::_toHtml();
	}
	public function getBaseMediaUrl()
	{
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}
	function dirFiles($directry) {
		$dir = dir($directry);
		$filesall = array();
		while (false!== ($file = $dir->read())) 
		{
			$extension = substr($file, strrpos($file, '.')); 
			if($extension == ".png" || $extension == ".gif" || $extension == ".jpg" |$extension == ".jpeg") 
				$filesall[$file] = $file; 
		}
		$dir->close(); // Close Directory
		asort($filesall); // Sorts the Array
		return $filesall;
	}

	public function getListProducts( $productIds = array()) {
		$collection = $this->_productCollectionFactory->create();
		$collection->addFieldToFilter('visibility', array(
			\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
			\Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG
			))
		->addAttributeToSelect('*')
		->addStoreFilter()
		->addIdFilter($productIds)
		->addAttributeToSort('updated_at', 'desc')
		->addMinimalPrice()
		->addFinalPrice()
		->addUrlRewrite()
		->addTaxPercents()
		->setCurPage(1)
		->groupByAttribute('entity_id');
		$this->_addProductAttributesAndPrices($collection);
		return $collection->getItems();
	}
}