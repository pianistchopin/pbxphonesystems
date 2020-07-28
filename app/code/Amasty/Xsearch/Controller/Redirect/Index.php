<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Controller\Redirect;

use Magento\Framework\App\Action\Context;
use Magento\Search\Model\QueryFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    const AMSEARCH_404_REDIRECT = 'amnoroute';

    /**
     * @var \Magento\Search\Helper\Data
     */
    private $searchHelper;

    /**
     * @var \Amasty\Xsearch\Model\System\Config
     */
    private $config;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Search\Helper\Data $searchHelper
     */
    public function __construct(
        Context $context,
        \Amasty\Xsearch\Model\Config $config,
        \Magento\Search\Helper\Data $searchHelper
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->searchHelper = $searchHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     * |\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $url = $this->searchHelper->getResultUrl($this->_request->getParam(QueryFactory::QUERY_VAR_NAME));

        $url .= strpos($url, '?') === false
            ? '?' . self::AMSEARCH_404_REDIRECT
            : '&' . self::AMSEARCH_404_REDIRECT;

        $resultRedirect->setUrl($url)
            ->setHttpResponseCode($this->config->getRedirectCode());

        return $resultRedirect;
    }
}
