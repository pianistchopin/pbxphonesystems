<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Controller\Adminhtml\Areas;

use Amasty\ShippingArea\Api\AreaRepositoryInterface;
use Amasty\ShippingArea\Api\Data\AreaInterface;
use Amasty\ShippingArea\Api\Data\AreaInterfaceFactory;
use Amasty\ShippingArea\Controller\Adminhtml\Areas;
use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Areas
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var AreaRepositoryInterface
     */
    private $areaRepository;

    /**
     * @var AreaInterfaceFactory
     */
    private $areaFactory;

    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        AreaRepositoryInterface $areaRepository,
        AreaInterfaceFactory $areaFactory
    ) {
        parent::__construct($context);

        $this->dataPersistor = $dataPersistor;
        $this->areaRepository = $areaRepository;
        $this->areaFactory = $areaFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        /** @var \Amasty\ShippingArea\Model\Area $areaModel */
        $areaModel = $this->areaFactory->create();

        try {
            if (!empty($data[AreaInterface::POSTCODE_SET]) && is_array($data[AreaInterface::POSTCODE_SET])) {
                foreach ($data[AreaInterface::POSTCODE_SET] as $key => $zipRow) {
                    if (isset($zipRow['delete']) && $zipRow['delete']) {
                        unset($data[AreaInterface::POSTCODE_SET][$key]);
                    }
                }
            }
            $areaModel->setData($data);
            $this->areaRepository->save($areaModel);

            $this->dataPersistor->clear(AreaInterface::FORM_NAMESPACE);
            $this->messageManager->addSuccessMessage(__('You saved the Shipping Area.'));

            if (!$this->getRequest()->getParam('back')) {
                return $this->_redirect('amasty_shiparea/areas/');
            }
        } catch (LocalizedException $exception) {
            $this->dataPersistor->set(AreaInterface::FORM_NAMESPACE, $data);

            $this->messageManager->addErrorMessage($exception->getMessage());

            if (!isset($data[AreaInterface::AREA_ID])) {
                return $this->_redirect('amasty_shiparea/areas/new');
            }
        } catch (\Exception $exception) {
            $this->dataPersistor->set(AreaInterface::FORM_NAMESPACE, $data);

            $this->messageManager->addExceptionMessage(
                $exception,
                __('Can\'t save the area right now. Please review the log and try again.')
            );

            if (!isset($data[AreaInterface::AREA_ID])) {
                return $this->_redirect('amasty_shiparea/areas/new');
            }
        }

        return $this->_redirect('amasty_shiparea/areas/edit', ['id' => $areaModel->getAreaId()]);
    }
}
