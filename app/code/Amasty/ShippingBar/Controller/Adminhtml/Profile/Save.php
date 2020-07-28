<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Controller\Adminhtml\Profile;

use Amasty\ShippingBar\Api\Data\ProfileInterface;
use Amasty\ShippingBar\Api\Data\ProfileInterfaceFactory;
use Amasty\ShippingBar\Api\ProfileRepositoryInterface;
use Amasty\ShippingBar\Controller\Adminhtml\AbstractProfile;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractProfile
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @var TypeListInterface
     */
    private $typeList;

    /**
     * @var ProfileInterfaceFactory
     */
    private $profileFactory;

    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        ProfileRepositoryInterface $profileRepository,
        TypeListInterface $typeList,
        ProfileInterfaceFactory $profileFactory
    ) {
        parent::__construct($context);

        $this->dataPersistor = $dataPersistor;
        $this->profileRepository = $profileRepository;
        $this->typeList = $typeList;
        $this->profileFactory = $profileFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();

        try {
            unset($data['id']);
            if ($id = (int)$this->getRequest()->getParam('id')) {
                $profileModel = $this->profileRepository->getById($id);
            } else {
                /** @var ProfileInterface $profileModel */
                $profileModel = $this->profileFactory->create();
            }
            $profileModel->addData($data);
            $this->profileRepository->save($profileModel);

            $this->typeList->invalidate(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
            $this->messageManager->addWarningMessage(__('Please, refresh the Full '
                . 'Page Cache for the changes to take effect.'));

            $this->dataPersistor->clear(ProfileInterface::FORM_NAMESPACE);
            $this->messageManager->addSuccessMessage(__('You saved the Shipping Bar.'));
            if (!$this->getRequest()->getParam('back')) {
                return $this->_redirect('amasty_shipbar/profile/');
            }
        } catch (LocalizedException $exception) {
            $this->dataPersistor->set(ProfileInterface::FORM_NAMESPACE, $data);

            $this->messageManager->addErrorMessage($exception->getMessage());

            if (!isset($data[ProfileInterface::ID])) {
                return $this->_redirect('amasty_shipbar/profile/new');
            }
        } catch (\Exception $exception) {
            $this->dataPersistor->set(ProfileInterface::FORM_NAMESPACE, $data);

            $this->messageManager->addExceptionMessage(
                $exception,
                __('Can\'t save the Shipping Bar right now. Please review the log and try again.')
            );

            if (!isset($data[ProfileInterface::ID])) {
                return $this->_redirect('amasty_shipbar/profile/new');
            }
        }

        return $this->_redirect('amasty_shipbar/profile/edit', ['id' => $profileModel->getProfileId()]);
    }
}
