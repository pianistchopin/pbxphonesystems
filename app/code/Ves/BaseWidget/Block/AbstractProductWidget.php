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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class AbstractProductWidget extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{
    var $_product = null;
    protected $_product_link;
    protected $_blockModel;
    protected $_dataFilterHelper;
    protected $_layout;
    protected $urlHelper;
    /** @var UrlFinderInterface */
    protected $urlFinder;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Cms\Model\Block $blockModel,
        \Ves\BaseWidget\Helper\Data $dataHelper,
        ProductRepositoryInterface $productRepository,
        UrlFinderInterface $urlFinder,
        array $data = []
        ) {

        parent::__construct($context, $data);

        $this->_blockModel = $blockModel;
        $this->_dataFilterHelper = $dataHelper;
        $this->_layout = $context->getLayout();
        $this->productRepository = $productRepository;
        $this->urlHelper = $urlHelper;
        $this->urlFinder = $urlFinder;
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

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
    public function getProduct(){
        if(!$this->_product) {
            $arr = explode('/', $this->getConfig('id_path'));
            $product_id = end($arr);
            if ($product_id) {
                $this->_product = $this->productRepository->getById($product_id);
                if(!$this->_product){
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance 
                    $this->_product = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id); 
                }
            }
        }
        return $this->_product;
    }

    public function getSingleLink($product, $params = []) {
        $categoryId = null;
        if(!$this->_product_link){
            $storeId = $product->getStoreId();
            if (!isset($params['_ignore_category']) && $product->getCategoryId() && !$product->getDoNotUseCategoryId()) {
                $categoryId = $product->getCategoryId();
            }
            $filterData = [
                UrlRewrite::ENTITY_ID => $product->getId(),
                UrlRewrite::ENTITY_TYPE => \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator::ENTITY_TYPE,
                UrlRewrite::STORE_ID => $storeId,
            ];
            if ($categoryId) {
                $filterData[UrlRewrite::METADATA]['category_id'] = $categoryId;
            }
            $rewrite = $this->urlFinder->findOneByData($filterData);
            if ($rewrite) {
                $requestPath = $rewrite->getRequestPath();
                $product->setRequestPath($requestPath);
            } else {
                $product->setRequestPath(false);
            }

            $this->_product_link = $product->getProductUrl();
        }

        return $this->_product_link;
    }




    /**
     * Check product is new
     *
     * @param  Mage_Catalog_Model_Product $_product
     * @return bool
     */
    public function checkProductIsNew($_product = null) {
        $from_date = $_product->getNewsFromDate();
        $to_date = $_product->getNewsToDate();
        $is_new = false;
        $is_new = $this->isNewProduct($from_date, $to_date);
        $today = strtotime("now");

        if ($from_date && $to_date) {
            $from_date = strtotime($from_date);
            $to_date = strtotime($to_date);
            if ($from_date <= $today && $to_date >= $today) {
                $is_new = true;
            }
        }
        elseif ($from_date && !$to_date) {
            $from_date = strtotime($from_date);
            if ($from_date <= $today) {
                $is_new = true;
            }
        }elseif (!$from_date && $to_date) {
            $to_date = strtotime($to_date);
            if ($to_date >= $today) {
                $is_new = true;
            }
        }
        return $is_new;
    }

    public function isNewProduct( $created_date, $num_days_new = 3) {
        $check = false;

        $startTimeStamp = strtotime($created_date);
        $endTimeStamp = strtotime("now");

        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff/86400;// 86400 seconds in one day

        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        if($numberDays <= $num_days_new) {
            $check = true;
        }
        return $check;
    }


    public function subString( $text, $length = 100, $replacer ='...', $is_striped=true ){
        $text = ($is_striped==true)?strip_tags($text):$text;
        if(strlen($text) <= $length){
            return $text;
        }
        $text = substr($text,0,$length);
        $pos_space = strrpos($text,' ');
        return substr($text,0,$pos_space).$replacer;
    }
}