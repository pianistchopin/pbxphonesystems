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
namespace Ves\BaseWidget\Controller\Adminhtml\Widget;

class LoadOptions extends \Magento\Widget\Controller\Adminhtml\Widget\LoadOptions
{
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;
    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $conditionsHelper;
     /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
        $this->conditionsHelper = $conditionsHelper;
        parent::__construct($context);
    }
	/**
     * Format widget pseudo-code for inserting into wysiwyg editor
     *
     * @return void
     */
	public function execute()
	{
		try {
            if ($paramsJson = $this->getRequest()->getParam('widget')) {
                $request = $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonDecode($paramsJson);
                if (is_array($request)) {
                    if (isset($request['values'])) {
                        $widget = $this->registry->registry('current_widget_instance');
                        if(!$widget && (isset($request['values']['conditions']) || isset($request['values']['conditions_encoded']))){
                            $widget_params = $request['values'];
                            $conditions = isset($widget_params['conditions_encoded'])
                                ? $widget_params['conditions_encoded']
                                : $widget_params['conditions'];

                            if ($conditions) {
                                $conditions = $this->conditionsHelper->decode($conditions);
                            }
                            $widget_params['conditions'] = $conditions;
                            $widget_object = new \Magento\Framework\DataObject();
                            $widget_object->setData("widget_parameters", $widget_params);
                            $this->registry->register('current_widget_instance', $widget_object);
                        }
                    }
                }
                parent::execute();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result = ['error' => true, 'message' => $e->getMessage()];
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
            );
        }
	}
	
}