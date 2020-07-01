<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Testimonial
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Testimonial\Block\Widget;

class CustomWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * Name of request parameter for page number value
     */
    const PAGE_VAR_NAME = 'ntmp';

    /**
     * Instance of pager block
     *
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    protected $_pager;

    /**
     * @var \Ves\Testimonial\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Ves\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory
     */
    protected $_testimonialCollectionFactory;
    /**
     * @param \Magento\Catalog\Block\Product\Context $context     
     * @param \Magento\Framework\Url\Helper\Data     $urlHelper   
     * @param array                                  $data        
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Ves\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory $testimonialCollectionFactory,
        \Ves\Testimonial\Helper\Data $_helper,
        array $data = []
        ) {
        $this->_helper = $_helper;
        $this->urlHelper = $urlHelper;
        $this->_testimonialCollectionFactory = $testimonialCollectionFactory;
        parent::__construct($context, $data);
    }
    public function _toHtml()
    {
        $enable = $this->_helper->getConfig('general/enable');
        if(!$enable) return;
        $template = '';
        $layout= $this->getConfig('layout');
        switch ($layout) {
            case 'topmeta':
            $template = 'Ves_Testimonial::widget/topmeta.phtml';
            break;
            case 'bottommeta':
            $template = 'Ves_Testimonial::widget/bottommeta.phtml';
            break;
            case 'alltop':
            $template = 'Ves_Testimonial::widget/alltop.phtml';
            break;
            case 'allbottom':
            $template = 'Ves_Testimonial::widget/allbottom.phtml';
            break;
            case 'topimage':
            $template = 'Ves_Testimonial::widget/topimage.phtml';
            break;
            case 'bottomimage':
            $template = 'Ves_Testimonial::widget/bottomimage.phtml';
            break;
            case 'style1':
            $template = 'Ves_Testimonial::widget/style1.phtml';
            break;
            case 'style2':
            $template = 'Ves_Testimonial::widget/style2.phtml';
            break;
            case 'style3':
            $template = 'Ves_Testimonial::widget/style3.phtml';
            break;
            case 'style4':
            $template = 'Ves_Testimonial::widget/style4.phtml';
            break;
            case 'slide1':
            $template = 'Ves_Testimonial::widget/slide1.phtml';
            break;
            case 'slide2':
            $template = 'Ves_Testimonial::widget/slide2.phtml';
            break;
            case 'grid':
            $template = 'Ves_Testimonial::widget/grid.phtml';
            break;
            case 'grid1':
            $template = 'Ves_Testimonial::widget/grid1.phtml';
            break;
            case 'grid2':
            $template = 'Ves_Testimonial::widget/grid2.phtml';
            break;
            case 'list':
            $template = 'Ves_Testimonial::widget/list.phtml';
            break;
        }
        if($blockTemplate = $this->getConfig('block_template')){
            $template = $blockTemplate;
        }
        $this->setTemplate($template);
        $orderBy = $this->getConfig('order_by');
        $item_per_page = (int)$this->getConfig('item_per_page');
        if(!$this->getConfig('grid_pagination') || $layout != 'grid' || $layout != 'grid1' || $layout !='grid2'){
            $item_per_page = $this->getConfig('number_item');
        }
        $testimonialCollection = $this->_testimonialCollectionFactory->create()
        ->setPageSize($item_per_page)
        ->addFieldToFilter('is_active', '1');
        if($orderBy == 'rating'){
            $testimonialCollection->setOrder('rating', 'DESC');
        }elseif($orderBy=='position'){
            $testimonialCollection->setOrder('position', 'ASC');
        }elseif($orderBy == 'random'){
            $testimonialCollection->getSelect()->order('rand()');
        }
        if($this->getConfig('grid_pagination') || $layout == 'grid' || $layout == 'grid1' || $layout == 'grid2'){
            $currentPage = $this->getCurrentPage();
            $testimonialCollection->setCurPage($currentPage);
        }
        $this->setTestimonialCollection($testimonialCollection);
        return parent::_toHtml();
    }

    /**
     * Get number of current page based on query value
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return abs((int)$this->getRequest()->getParam(self::PAGE_VAR_NAME));
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        $numberItem = (int)$this->getConfig('number_item');
        $item_per_page = (int)$this->getConfig('item_per_page');
        $name = 'ves.testimonial.widget' . time() . uniqid();
            if (!$this->_pager) {
                $this->_pager = $this->getLayout()->createBlock(
                    'Magento\Catalog\Block\Product\Widget\Html\Pager',
                    $name
                );
                $this->_pager->setUseContainer(true)
                    ->setShowAmounts(false)
                    ->setShowPerPage(false)
                    ->setPageVarName(self::PAGE_VAR_NAME)
                    ->setLimit($item_per_page)
                    ->setTotalLimit($numberItem)
                    ->setCollection($this->getTestimonialCollection());
            }
            if ($this->_pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->_pager->toHtml();
            }
    }

    public function setTestimonialCollection($collection){
        $this->_collection = $collection;
        return $this;
    }

    public function getTestimonialCollection()
    {
        return $this->_collection;
    }
    
    public function getConfig($key, $default = '')
    {
        if($this->hasData($key) && $this->getData($key))
        {
            return $this->getData($key);
        }
        return $default;
    }
}