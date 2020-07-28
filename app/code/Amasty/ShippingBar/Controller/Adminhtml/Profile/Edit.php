<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Controller\Adminhtml\Profile;

use Amasty\ShippingBar\Controller\Adminhtml\AbstractProfile;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends AbstractProfile
{
    /**
     * @var \Amasty\ShippingBar\Api\ProfileRepositoryInterface
     */
    private $profileRepository;

    public function __construct(
        Action\Context $context,
        \Amasty\ShippingBar\Api\ProfileRepositoryInterface $profileRepository
    ) {
        parent::__construct($context);

        $this->profileRepository = $profileRepository;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $profileId = $this->getRequest()->getParam('id');

        if ($profileId) {
            try {
                $profileModel = $this->profileRepository->getById($profileId);
                $resultPage->getConfig()->getTitle()->prepend($profileModel->getName());
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());

                return $this->_redirect('amasty_shipbar/profile/');
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __(
                        'Unable to load Shipping Bar with ID %1. '
                        . 'Please review the log and try again.',
                        $profileId
                    )
                );

                return $this->_redirect('amasty_shipbar/profile/');
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Shipping Bar'));
        }

        return $resultPage;
    }
}
