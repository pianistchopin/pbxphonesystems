<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;

/**
 * Skeleton for Duplicate Action.
 */
abstract class AbstractDuplicate extends Action
{
    /**
     * @var \Amasty\CommonRules\Model\Rule
     */
    private $ruleModel;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    private $resource;

    public function __construct(
        Action\Context $context,
        \Amasty\CommonRules\Model\Rule $ruleModel,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
    ) {
        parent::__construct($context);

        $this->ruleModel = $ruleModel;
        $this->resource = $resource;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $modelId = $this->getRequest()->getParam('id');

        if (!$modelId) {
            $this->messageManager->addErrorMessage(__('Please select a rule to duplicate.'));

            return $this->_redirect('*/*');
        }
        try {
            $this->resource->load($this->ruleModel, $modelId);
            if (!$this->ruleModel->getId()) {
                $this->messageManager->addErrorMessage(__('This item no longer exists.'));

                return $this->_redirect('*/*');
            }

            $this->ruleModel->setIsActive(0);
            $this->ruleModel->setId(null);
            $this->resource->save($this->ruleModel);

            $this->messageManager->addSuccessMessage(
                __('The rule has been duplicated. Please feel free to activate it.')
            );

            return $this->_redirect('*/*/edit', ['id' => $this->ruleModel->getId()]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $this->_redirect('*/*');
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while saving the item data. Please review log and try again.')
            );

            return $this->_redirect('*/*');
        }
    }
}
