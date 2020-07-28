<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Controller\Adminhtml\Areas;

use Amasty\ShippingArea\Api\AreaRepositoryInterface;
use Amasty\ShippingArea\Controller\Adminhtml\Areas;
use Magento\Backend\App\Action;

class Duplicate extends Areas
{
    /**
     * @var AreaRepositoryInterface
     */
    private $areaRepository;

    public function __construct(
        Action\Context $context,
        AreaRepositoryInterface $areaRepository
    ) {
        parent::__construct($context);

        $this->areaRepository = $areaRepository;
    }

    public function execute()
    {
        $areaId = $this->getRequest()->getParam('id');

        if ($areaId) {
            try {
                /** @var \Amasty\ShippingArea\Api\Data\AreaInterface $areaModel */
                $areaModel = $this->areaRepository->getById($areaId);
                $areaModel->setAreaId(null);
                $areaModel->setIsEnabled(\Amasty\ShippingArea\Model\System\StatusOptionProvider::STATUS_INACTIVE);
                $this->areaRepository->save($areaModel);
                $newId = $areaModel->getAreaId();

                $this->messageManager->addSuccessMessage(
                    __('You created new Shipping Area with ID %1 (ID of origin is %2).', $newId, $areaId)
                );

                return $this->_redirect('amasty_shiparea/areas/edit', ['id' => $newId]);
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We can\'t duplicate the Shipping Area right now. Please review the log and try again.')
                );

                return $this->_redirect('amasty_shiparea/areas/edit', ['id' => $areaId]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Shipping Area to duplicate.'));

        return $this->_redirect('amasty_shiparea/areas/');
    }
}
