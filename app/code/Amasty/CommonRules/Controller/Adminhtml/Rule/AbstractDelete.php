<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;

/**
 * Skeleton for Delete Action.
 */
abstract class AbstractDelete extends Action
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

    public function execute()
    {
        $ruleId = $this->getRequest()->getParam('id');

        if ($ruleId) {
            try {
                $this->resource
                    ->load($this->ruleModel, $ruleId)
                    ->delete($this->ruleModel);
                $this->messageManager->addSuccessMessage(__('You deleted the item.'));

                return $this->_redirect('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception, $exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We can\'t delete item right now. Please review log and try again.')
                );

                return $this->_redirect('*/*/edit', ['id' => $ruleId]);
            }

            return $this->_redirect('*/*/');
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an item to delete.'));

        return $this->_redirect('*/*/');
    }
}
