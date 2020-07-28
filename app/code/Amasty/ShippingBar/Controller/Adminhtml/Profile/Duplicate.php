<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Controller\Adminhtml\Profile;

use Amasty\ShippingBar\Api\ProfileRepositoryInterface;
use Amasty\ShippingBar\Controller\Adminhtml\AbstractProfile;
use Amasty\ShippingBar\Model\ResourceModel\Label as LabelResource;
use Magento\Backend\App\Action\Context;

class Duplicate extends AbstractProfile
{
    /**
     * @var ProfileRepositoryInterface
     */
    private $repository;

    /**
     * @var LabelResource
     */
    private $labelResource;

    public function __construct(
        Context $context,
        ProfileRepositoryInterface $repository,
        LabelResource $labelResource
    ) {
        parent::__construct($context);

        $this->repository = $repository;
        $this->labelResource = $labelResource;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                /** @var \Amasty\ShippingBar\Api\Data\ProfileInterface $model */
                $model = $this->repository->getById($id);

                $this->repository->addLabelsToProfile($model);

                $model->setId(null);
                $model->setStatus(0);
                $this->repository->save($model);
                $newId = $model->getId();

                $this->messageManager->addSuccessMessage(
                    __('You created new Bar with ID %1 (ID of origin is %2).', $newId, $id)
                );

                return $this->_redirect('*/*/edit', ['id' => $newId]);
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We can\'t duplicate the Bar right now. Please review the log and try again.')
                );

                return $this->_redirect('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Bar to duplicate.'));

        return $this->_redirect('*/*/');
    }
}
