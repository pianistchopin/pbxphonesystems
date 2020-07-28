<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Controller\Adminhtml\Rule;

use Amasty\CommonRules\Model\Rule;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb as Resource;
use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Skeleton for save.
 */
abstract class AbstractSave extends Action
{
    /**
     * @var string
     */
    protected $dataPersistorKey = '';

    /**
     * @var Rule
     */
    private $ruleModel;

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    public function __construct(
        Action\Context $context,
        Rule $ruleModel,
        Resource $resource,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);

        $this->ruleModel = $ruleModel;
        $this->resource = $resource;
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        if ($data = $this->getRequest()->getParams()) {
            $ruleId = (int) $this->getRequest()->getParam('rule_id');

            try {
                if ($ruleId) {
                    $this->resource->load($this->ruleModel, $ruleId);
                }

                $this->prepareData($data);

                $this->ruleModel->addData($data);
                $this->ruleModel->loadPost($data); // rules

                $this->resource->save($this->ruleModel);

                $this->messageManager->addSuccessMessage(__('You saved the rule.'));

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', ['id' => $this->ruleModel->getId()]);
                }

                return $this->_redirect('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception, $exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while saving the rule data. Please review log and try again.')
                );
            }
            $this->dataPersistor->set($this->dataPersistorKey, $this->getRequest()->getParams());

            if (!empty($ruleId)) {
                return $this->_redirect('*/*/edit', ['id' => $ruleId]);
            } else {
                return $this->_redirect('*/*/new');
            }
        }

        return $this->_redirect('*/*/');
    }

    abstract protected function prepareData(&$data);
}
