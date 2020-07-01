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
use Magento\Catalog\Api\CategoryRepositoryInterface;

class Categoriesinfo extends AbstractWidget{


	protected $_storeManager;
	protected $_blockModel;
	protected $_dataFilterHelper;
	protected $_imageHelper;
	/**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		/*\Magento\Store\Model\StoreManagerInterface $storeManager,*/
		\Ves\BaseWidget\Helper\Data $dataHelper,
		\Ves\BaseWidget\Helper\Image $imageHelper,
		CategoryRepositoryInterface $categoryRepository,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);
		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		$this->_imageHelper = $imageHelper;
		/*$this->_storeManager = $storeManager;*/
		$this->categoryRepository = $categoryRepository;

		if($this->hasData("template")) {
			$my_template = $this->getData("template");
		}else{
			$my_template = "widget/categories_info.phtml";
		}
		$this->setTemplate($my_template);
	}
		
	public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

	public function subString( $text, $length = 100, $replacer = '...', $is_striped=true ){
		$text = ($is_striped==true)?strip_tags($text):$text;
		if(strlen($text) <= $length){
			return $text;
		}
		$text = substr($text,0,$length);
		$pos_space = strrpos($text,' ');
		return substr($text,0,$pos_space).$replacer;
	}

	public function getCategoryImage($category = null, $width = 300, $height = 300)
	{
		if(empty($category) && !is_object($category)) return "";

		$_file_name = $category->getImage();
		
		$_file_path = $this->getBaseMediaUrl() ."catalog/category/".$_file_name;
		
		if($_file_name) {
			return $this->_imageHelper->resizeImage($_file_path, (int)$width, (int)$height);
		}
		return "";
	}

	public function getCategoryInfo( $categoryId = 0 ){
    	if(!$categoryId)
    		return false;

    	try {
            $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return $category;
    }
    public function getChildCategories( $categoryId = 0, $limit = 3) {
    	$category_collection = $this->categoryRepository->getCollection();
    	$category_collection->addAttributeToSelect('name')
							->addFieldToFilter('parent_id', $categoryId)
							->addAttributeToFilter('is_active',1)
							->addAttributeToSort('position', ASC)
							->setPageSize($limit);
		return $category_collection;

    }

	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$cms = "";

 		$cms_block_id = $this->getConfig('cmsblock');
 		if($cms_block_id){
 			$cms = $this->_blockModel->load($cms_block_id)->getContent();
		 	$cms = $this->_dataFilterHelper->filter($cms);
 		}
 		$pretext = $this->getConfig("pretext");
		$pretext = str_replace(" ","+",$pretext);
		if(base64_decode($pretext, true) == true){
		   	$pretext = base64_decode($pretext);
		} else {
			$pretext = $this->getConfig("pretext");
		}
		if($pretext) $pretext = $this->_dataFilterHelper->filter($pretext);

		$this->assign('pretext', $this->_dataFilterHelper->filter($pretext));
 		$this->assign('cms', $cms);
		$this->assign('widget_heading', $this->getConfig('title'));
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('resize_image', $this->getConfig("resize_image"));
		$this->assign('image_width', $this->getConfig("image_width"));
		$this->assign('image_height', $this->getConfig("image_height"));
		$this->assign('catsid', $this->getConfig("catsid"));
		$this->assign('show_title', $this->getConfig("show_title"));
		$this->assign('show_description', $this->getConfig("show_description"));
		$this->assign('limit_description', (int)$this->getConfig("limit_description"));
		$this->assign('show_sub_category', $this->getConfig("show_sub_category"));
		$this->assign('limit_subcategory', (int)$this->getConfig("limit_subcategory"));
		$this->assign('show_number_product', $this->getConfig("show_number_product"));
		$this->assign('show_image', $this->getConfig("show_image"));
		$this->assign('limit', (int)$this->getConfig("limit"));
		$this->assign('columns', (int)$this->getConfig("columns"));
		$this->assign('show_viewall', $this->getConfig("show_viewall"));

		return parent::_toHtml();
	}

}