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
namespace Ves\PageBuilder\Controller\Adminhtml\Blockbuilder;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
/**
 * Class MassDisable
 */
class ConvertTemplate extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    protected $resultPageFactory;

    protected $builderHelper;
    protected $_logger;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Ves\PageBuilder\Helper\Builder $builderHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
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
        $id = $this->getRequest()->getParam('block_id');
        if($id) {
            try {
                $profile = $this->_objectManager->create('Ves\PageBuilder\Model\Block');
                $profile->load($id);
                $collection = [];
                $collection[] = $profile;
                $this->convertProfiles($collection);
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */    
                $this->messageManager->addSuccess(__('The element builder profile <strong>%1</strong> have been convert to html content of CMS Block: Ves_Element_%2', $profile->getTitle(), $profile->getTitle()));

            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
            }
        }
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    public function convertProfiles($collection) {
        foreach ($collection as $item) {
            $settings = ['block_name' => 'builder_element_template',
                            'template' => 'builder/block_container.phtml'
                        ];
            $template_content = $this->builderHelper->generateHtml($item, $settings);
            $this->createCMSBlock($item, $template_content);
        }
    }
    public function createCMSBlock($profile, $html_content = '') {
        // Start Create Or Updated CMS Block
        $data = array();
        $prefix_title = "Ves_Element_";
        $prefix_alias = "ves_element_";
        $alias = $prefix_alias;
        $alias .= $profile->getAlias();

        $model = $this->_objectManager->create('Ves\PageBuilder\Model\Block');
        $block = $model->loadCMSBlock($alias, "identifier", $profile->getStoreId());
        $first_store_id = $profile->getData("_first_store_id");
        if(!$first_store_id) {
            $first_store_id = isset($page_stores[0])?$page_stores[0]:0;
        }
        $page_stores = $profile->getStoreId();
        $data['block_id'] = $block->getBlockId();
        $data['title'] = $prefix_title."".$profile->getTitle();
        $data['identifier'] = $alias;
        $data['is_active'] = $profile->getStatus();
        $data['stores'] = $profile->getStoreId();
        $data['store_id'] = $profile->getStoreId();
        $data['_first_store_id'] = $first_store_id;

        $data['content'] = $html_content;

        //init model and set data
        $block_model = $this->_objectManager->create('Magento\Cms\Model\Block');
        if ($id = $block->getBlockId()) {
            $block_model->load($id);
        }
        $block_model->setData($data);

        $this->_eventManager->dispatch(
            'cms_block_prepare_save',
            ['page' => $block_model, 'request' => $this->getRequest()]
        );

        try {
            $block_model->save();
            $this->messageManager->addSuccess(__('The Element Builder Profile Was Converted To CMS Html Content.'));
            $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->addDebug($e);
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->_logger->addDebug($e);
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->addDebug($e);
            $this->messageManager->addException($e, __('Something went wrong while convert the element profile.'));
        }
        return true;
    }
    
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ves_PageBuilder::block_convert_template');
    }
}
