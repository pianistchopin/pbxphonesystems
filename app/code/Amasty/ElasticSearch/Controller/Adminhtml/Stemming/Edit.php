<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Controller\Adminhtml\Stemming;

use Magento\Framework\Controller\ResultFactory;
use Amasty\ElasticSearch\Api\Data\StemmedWordInterface;

/**
 * Class Edit
 * @package Amasty\ElasticSearch\Controller\Adminhtml\Stemming
 */
class Edit extends AbstractStemmedWord
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $stemmedWordId = (int)$this->getRequest()->getParam(StemmedWordInterface::STEMMED_WORD_ID);

        try {
            if ($stemmedWordId) {
                /** @var  \Amasty\ElasticSearch\Model\StemmedWord $model */
                $model = $this->stemmedWordRepository->getById($stemmedWordId);
            } else {
                $model = $this->stemmedWordFactory->create();
            }

        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage(__('This Stemmed Word no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $text = $model->getId() ? __('Edit Stemmed Word "%1"', $model->getStemmedWord()) : __('New Stemmed Word');
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend($text);

        return $resultPage;
    }
}
