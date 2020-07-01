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

use Magento\Backend\App\Action;
class Grid extends \Magento\Backend\App\Action
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Brand list action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        try
        {
            $id = $this->getRequest()->getParam('block_id');
            $id = $id?(int)$id:0;
            $html = "";
            $model = $this->_objectManager->create('Ves\PageBuilder\Model\Block');
            $model->load($id);

            $this->_coreRegistry->register('ves_pagebuilder', $model);

            $resultPage = $this->resultPageFactory->create();
            $builder_content_block = $resultPage->getLayout()->createBlock(
                    'Ves\BaseWidget\Block\Adminhtml\InitBuilder',
                    'init.builder'
                );
            $builder_content_block->setTemplate("Ves_BaseWidget::builder/load_element_form.phtml");

            $html = $builder_content_block->toHtml();
            
            $this->getResponse()->setBody( $html );
            
        }
        catch ( Exception $ex )
        {
            $this->getResponse()->setBody( $ex->getMessage() );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ves_PageBuilder::block_edit');
    }
    
}