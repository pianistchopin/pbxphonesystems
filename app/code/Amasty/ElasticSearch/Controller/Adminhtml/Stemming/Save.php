<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Controller\Adminhtml\Stemming;

use Amasty\ElasticSearch\Api\Data\StemmedWordInterface;

/**
 * Class Save
 */
class Save extends AbstractStemmedWord
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $stemmedWordId = (int)$this->getRequest()->getParam(StemmedWordInterface::STEMMED_WORD_ID);

        try {
            if ($stemmedWordId) {
                /** @var  \Amasty\ElasticSearch\Model\StemmedWord $model */
                $model = $this->stemmedWordRepository->getById($stemmedWordId);
            } else {
                $model = $this->stemmedWordFactory->create();
            }

            $model->setStemmedWord($this->getRequest()->getParam(StemmedWordInterface::STEMMED_WORD))
                ->setWords($this->getRequest()->getParam(StemmedWordInterface::WORDS))
                ->setStoreId($this->getRequest()->getParam(StemmedWordInterface::STORE_ID));
            $this->stemmedWordRepository->save($model);
            $this->messageManager->addSuccessMessage(__('You have saved the Stemmed Word.'));
            $this->indexerRegistry->get(\Magento\CatalogSearch\Model\Indexer\Fulltext::INDEXER_ID)->invalidate();
        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            $this->messageManager->addErrorMessage(
                __('A Stemmed Word with the same term already exists in an associated store.')
            );
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage(__('This Stemmed Word no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();
        }

        return $resultRedirect->setPath('*/*/');
    }
}
