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
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Areas
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
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $areaModel = $this->areaRepository->getById($id);
                $resultPage->getConfig()->getTitle()->prepend($areaModel->getName());
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());

                return $this->_redirect('amasty_shiparea/areas/');
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Unable to load Shipping Area with ID %1. Please review the log and try again.', $id)
                );

                return $this->_redirect('amasty_shiparea/areas/');
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Shipping Area'));
        }

        return $resultPage;
    }
}
