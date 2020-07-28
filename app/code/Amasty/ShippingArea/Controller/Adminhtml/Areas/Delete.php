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

class Delete extends Areas
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
                $this->areaRepository->deleteById($areaId);

                $this->messageManager->addSuccessMessage(__('You deleted the Shipping Area.'));

                return $this->_redirect('amasty_shiparea/areas/');
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We can\'t delete the Shipping Area right now. Please review the log and try again.')
                );

                return $this->_redirect('amasty_shiparea/areas/edit', ['id' => $areaId]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Shipping Area to delete.'));

        return $this->_redirect('amasty_shiparea/areas/');
    }
}
