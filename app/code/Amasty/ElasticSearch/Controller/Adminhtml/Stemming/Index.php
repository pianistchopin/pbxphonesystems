<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Controller\Adminhtml\Stemming;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Amasty\ElasticSearch\Controller\Adminhtml\Stemming
 */
class Index extends AbstractStemmedWord
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)
            ->getConfig()->getTitle()->prepend(__('Manage Stemmed Words'));

        return $resultPage;
    }
}
