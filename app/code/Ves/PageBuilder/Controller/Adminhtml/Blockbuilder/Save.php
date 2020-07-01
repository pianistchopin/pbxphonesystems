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

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Ves\PageBuilder\Helper\Data
     */
    protected $_viewHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context, 
        /*\Magento\Framework\ObjectManagerInterface $objectManager,*/
        \Ves\PageBuilder\Helper\Data $dataHelper,
        \Magento\Framework\Filesystem $filesystem
        ) {
        /*$this->_objectManager = $objectManager;*/
        $this->_fileSystem = $filesystem;
        $this->_viewHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
    	return $this->_authorization->isAllowed('Ves_PageBuilder::block_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	$post_data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($post_data) {
            //Duplicate Block Builder Profile
            if ($this->getRequest()->getParam("duplicate")) {
                $model_from = $this->_objectManager->create('Ves\PageBuilder\Model\Block');
                $model = $this->_objectManager->create('Ves\PageBuilder\Model\Block');

                $id = $this->getRequest()->getParam('block_id');
                if ($id) {
                    $model_from->load($id);
                }
                
                $block_id = 0;
                $block_data = ['shortcode' => $model_from->getShortcode(),
                                 'params' => $model_from->getParams(),
                                 'layout_html' => $model_from->getLayoutHtml(),
                                 'title' => $model_from->getTitle()."-clone",
                                 'alias' => $model_from->getAlias()."-clone",
                                 'status' => $model_from->getStatus(),
                                 'block_type' => $model_from->getBlockType(),
                                 'container' => $model_from->getContainer(),
                                 'prefix_class' => $model_from->getPrefixClass(),
                                 'show_from' => $model_from->getShowFrom(),
                                 'show_to' => $model_from->getShowTo(),
                                 'settings' => $model_from->getSettings(),
                                 'customer_group' => $model_from->getCustomerGroup(),
                                 'created' => date( 'Y-m-d H:i:s' ),
                                 'position' => $model_from->getPosition()
                                ];

                //Duplicate widget shortcodes                
                $block_widgets = $model_from->lookupWidgets($id);
                if($block_widgets) {
                    $wpowidgets = [];
                    foreach($block_widgets as $widget) {
                        $tmp = [];
                        $tmp['config'] = $widget['widget_shortcode'];
                        $wpowidgets[$widget['widget_key']] = $tmp;
                    }
                    $block_data['wpowidget'] = $wpowidgets;
                }
                
                $settings = [];
                $block_data['shortcode'] = $this->_viewHelper->getShortCode("Ves\PageBuilder\Block\Widget\Builder", $this->getRequest()->getParam("block_id"), $settings);
                
                $model->setData($block_data);

            } else {
                $model = $this->_objectManager->create('Ves\PageBuilder\Model\Block');

                $id = $this->getRequest()->getParam('block_id');
                if ($id) {
                    $model->load($id);
                }

                $settings = [];
                $post_data['customer_group'] = isset($post_data['customer_group'])?$post_data['customer_group']:[];
                $post_data['block_type'] = isset($post_data['block_type'])?$post_data['block_type']:'block';
                $post_data['container'] = isset($post_data['container'])?$post_data['container']:'1';
                $post_data['customer_group'] = implode(',', $post_data['customer_group']);
                $post_data['params'] = str_replace(array("<p>","</p>"), "", $post_data['params'] );
                $post_data['params'] = trim($post_data['params']);

                //$post_data['settings'] = (isset($post_data['settings']) && $post_data['settings'])?serialize($post_data['settings']):"";
                $post_data['shortcode'] = $this->_viewHelper->getShortCode("Ves\PageBuilder\Block\Widget\Builder", $this->getRequest()->getParam("block_id"), $settings);

                if($this->getRequest()->getParam("block_id")) {
                    $post_data['modified'] = date( 'Y-m-d H:i:s' );
                } else {
                    $post_data['created'] = date( 'Y-m-d H:i:s' );
                }

                $model->setData($post_data);
            }

            try {
                $model->save();
                if ($this->getRequest()->getParam("duplicate")) {
                    $this->messageManager->addSuccess(__('Profile was successfully duplicated.'));
                } else {
                    $this->messageManager->addSuccess(__('You saved this block builder profile.'));
                }

                //If auto backup profile was enabled, then auto save current page builder profile to var/vespagebuilder/ folder
                if($this->_viewHelper->getConfig('general/auto_backup_profile')) {
                    $this->_viewHelper->autoBackupLayoutProfile( $post_data, "vesblockbuilder" );
                }
                
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['block_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the block builder profile.'));
            }
            $this->_getSession()->setFormData($post_data);
            return $resultRedirect->setPath('*/*/edit', ['block_id' => $this->getRequest()->getParam('block_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

}