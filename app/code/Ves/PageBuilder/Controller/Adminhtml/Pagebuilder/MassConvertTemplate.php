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
 * @package    Ves_Brand
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\PageBuilder\Controller\Adminhtml\Pagebuilder;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ves\PageBuilder\Model\ResourceModel\Block\CollectionFactory;

/**
 * Class MassDisable
 */
class MassConvertTemplate extends \Magento\Backend\App\Action
{
    /**
     * Entity type code
     */
    const ENTITY_TYPE = 'cms-page';
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    protected $dataProcessor;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    protected $builderHelper;
    protected $_logger;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context,
                                Filter $filter, 
                                CollectionFactory $collectionFactory, 
                                PostDataProcessor $dataProcessor,
                                \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
                                \Ves\PageBuilder\Helper\Builder $builderHelper,
                                \Psr\Log\LoggerInterface $logger
                                )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->dataProcessor = $dataProcessor;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->builderHelper = $builderHelper;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collection->loadBuilderWidgets();

            $this->convertProfiles($collection);
            
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */    
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been convert to html content of CMS Pages have prefix: Ves_Template_', $collection->getSize()));

        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());
            // go back to edit form
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
    public function convertProfiles($collection) {
        foreach ($collection as $item) {
            $settings = ['block_name' => 'builder_element_template',
                            'template' => 'builder/page.phtml'
                        ];
            $template_content = $this->builderHelper->generateHtml($item, $settings);
            $this->createCMSPage($item, $template_content);
        }
    }
    public function createCMSPage($profile, $html_content = '') {
        // Start Create Or Updated CMS Page
        $data = array();
        $prefix_title = "Ves_Template_";
        $prefix_alias = "ves_template_";
        $alias = $prefix_alias;
        $alias .= $profile->getAlias();

        $model = $this->_objectManager->create('Ves\PageBuilder\Model\Block');
        $page = $model->loadCMSPage($alias, "identifier", $profile->getStoreId());
        $first_store_id = $profile->getData("_first_store_id");
        if(!$first_store_id) {
            $first_store_id = isset($page_stores[0])?$page_stores[0]:0;
        }
        $page_stores = $profile->getStoreId();
        $custom_from_date = $profile->getCustomThemeFrom();
        $custom_to_date = $profile->getCustomThemeTo();
        $custom_from_date = date("m/d/Y", strtotime($custom_from_date));
        $custom_to_date = date("m/d/Y", strtotime($custom_to_date));
        $data['page_id'] = $page->getPageId();
        $data['title'] = $prefix_title."".$profile->getTitle();
        $data['identifier'] = $alias;
        $data['is_active'] = $profile->getStatus();
        $data['stores'] = $profile->getStoreId();
        $data['content_heading'] = "";
        $data['store_id'] = $profile->getStoreId();
        $data['_first_store_id'] = $first_store_id;

        $data['content'] = $html_content;
        $data['page_layout'] = $profile->getPageLayout();
        $data['layout_update_xml'] = $profile->getLayoutUpdateXml();
        $data['meta_keywords'] = $profile->getMetaKeywords();
        $data['meta_description'] = $profile->getMetaDescription();
        $data['custom_theme_from'] = $custom_from_date;
        $data['custom_theme_to'] = $custom_to_date;
        $data['custom_theme'] = $profile->getCustomTheme();
        $data['custom_root_template'] = $profile->getCustomRootTemplate();
        $data['custom_layout_update_xml'] = $profile->getCustomLayoutUpdateXml();

        $data = $this->dataProcessor->filter($data);
        //init model and set data
        $page_model = $this->_objectManager->create('Magento\Cms\Model\Page');
        if ($id = $data['page_id']) {
            $page_model->load($id);
        }
        $page_model->setData($data);

        $this->_eventManager->dispatch(
            'cms_page_prepare_save',
            ['page' => $page_model, 'request' => $this->getRequest()]
        );

        //Delete old rewrite url
        if(!$page_model->getId()) {
            $this->removeCMSUrlRewrite($data['stores'], $data['identifier']);
        }
        
        if (!$this->dataProcessor->validate($data)) {
            return false;
        }

        try {
            $page_model->save();
            $this->messageManager->addSuccess(__('The Page Builde Profile Was Converted To CMS Html Content.'));
            $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->addDebug($e);
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->_logger->addDebug($e);
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->addDebug($e);
            $this->messageManager->addException($e, __('Something went wrong while convert the page profile.'));
        }
        return true;
    }
    /**
     * Create url rewrite object
     *
     * @param int $storeId
     * @param int $redirectType
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite
     */
    protected function removeCMSUrlRewrite($storeId, $identifier)
    {
        $urlRewrite = $this->_objectManager->create('Magento\UrlRewrite\Model\UrlRewrite');
        $collection = $urlRewrite->getCollection();
        $collection->addFieldToFilter("entity_type", self::ENTITY_TYPE)
                   ->addFieldToFilter("request_path", $identifier);
        if($storeId && (count($storeId) > 1 || (count($storeId) == 1 && $storeId[0] != 0))){
           $collection->addFieldToFilter("store_id", array("in"=> $storeId)); 
        }
    
        if(0 < $collection->getSize()) {
            foreach($collection->getItems() as $item) {
                $urlRewriteItem = $this->_objectManager->create('Magento\UrlRewrite\Model\UrlRewrite');
                $urlRewriteItem->load($item->getId());
                $urlRewriteItem->delete();
            }
        }
    }
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ves_PageBuilder::page_convert_template');
    }
}
