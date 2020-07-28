<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Controller\Adminhtml\Profile;

use Amasty\ShippingBar\Api\ProfileRepositoryInterface;
use Amasty\ShippingBar\Controller\Adminhtml\AbstractProfile;
use Magento\Backend\App\Action;

class Delete extends AbstractProfile
{
    /**
     * @var ProfileRepositoryInterface
     */
    private $repository;

    public function __construct(
        Action\Context $context,
        ProfileRepositoryInterface $repository
    ) {
        parent::__construct($context);

        $this->repository = $repository;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->repository->deleteById($id);

                $this->messageManager->addSuccessMessage(__('You deleted the Bar.'));

                return $this->_redirect('*/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We can\'t delete the Bar right now. Please review the log and try again.')
                );

                return $this->_redirect('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Bar to delete.'));

        return $this->_redirect('*/*/');
    }
}
