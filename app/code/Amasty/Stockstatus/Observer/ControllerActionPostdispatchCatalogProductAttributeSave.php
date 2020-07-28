<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Observer;

use Magento\Framework\Event\ObserverInterface;

class ControllerActionPostdispatchCatalogProductAttributeSave implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Amasty\Stockstatus\Helper\Data
     */
    public $_helper;
    public $_imageHelper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Stockstatus\Helper\Data $helper,
        \Amasty\Stockstatus\Helper\Image $imageHelper
    )
    {
        $this->_objectManager = $objectManager;
        $this->_helper = $helper;
        $this->_imageHelper = $imageHelper;
    }

    /**
     * Predispath admin action controller
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $ranges =  $observer->getRequest()->getParam('amstockstatus_range');
        if ($ranges && is_array($ranges) && !empty($ranges))
        {
            $model = $this->_objectManager->create('Amasty\Stockstatus\Model\Ranges');
            $model->clear();

            foreach ($ranges as $range)
            {
                $data = [
                    'qty_from'   => $range['from'],
                    'qty_to'     => $range['to'],
                    'status_id'  => $range['status'],
                ];
                if ( $this->_helper->getRulesEnabled()
                    && array_key_exists('rule', $range)
                ) {
                    $data['rule'] = $range['rule'];
                }
                $model->setData($data);
                $model->save();
            }
        }
        /**
         * Deleting
         */
        $toDelete = $observer->getRequest()->getParam('amstockstatus_icon_delete');
        if ($toDelete) {
            foreach ($toDelete as $optionId => $del) {
                if ($del) {
                    $this->_imageHelper->delete($optionId);
                }
            }
        }

        /**
         * Uploading files
         */
        if ($observer->getRequest()->getFiles('amstockstatus_icon')) {
            $files = $observer->getRequest()->getFiles('amstockstatus_icon');
            foreach ($files as $optionId => $file) {
                if (isset($file['name']) && UPLOAD_ERR_OK == $file['error']) {
                    $this->_imageHelper->delete($optionId);
                    $this->_imageHelper->uploadImage($optionId, $file);
                }
            }
        }
    }
}
