<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Controller\Adminhtml\Rule;

use Amasty\CommonRules\Model\Rule;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Skeleton for Edit Action.
 */
abstract class AbstractEdit extends Action
{
    /**
     * @var string
     */
    protected $registryKey = '';

    /**
     * @var Rule
     */
    private $ruleModel;

    /**
     * @var AbstractDb
     */
    private $resource;

    /**
     * @var Registry
     */
    private $coreRegistry;

    public function __construct(
        Action\Context $context,
        Rule $ruleModel,
        AbstractDb $resource,
        Registry $coreRegistry
    ) {
        parent::__construct($context);

        $this->ruleModel = $ruleModel;
        $this->resource = $resource;
        $this->coreRegistry = $coreRegistry;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);

        $ruleId = $this->getRequest()->getParam('id');

        $this->coreRegistry->register($this->registryKey, $this->ruleModel);

        if ($ruleId) {
            try {
                $this->resource->load($this->ruleModel, $ruleId);
                $resultPage->getConfig()->getTitle()->prepend($this->ruleModel->getName());
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());

                return $this->_redirect('*/*');
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    $this->getErrorMessage($ruleId)
                );

                return $this->_redirect('*/*');
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend($this->getDefaultTitle());
        }

        return $resultPage;
    }

    /**
     * @return Phrase
     */
    abstract protected function getDefaultTitle();

    /**
     * @param int $ruleId
     *
     * @return Phrase
     */
    abstract protected function getErrorMessage($ruleId);
}
