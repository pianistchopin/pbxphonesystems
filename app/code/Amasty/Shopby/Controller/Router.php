<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Router
 * @package Amasty\Shopby\Controller
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    private $actionFactory;

    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    private $helper;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Amasty\Shopby\Helper\Data $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
    }

    /**
     * @param RequestInterface $request
     * @return bool|\Magento\Framework\App\ActionInterface
     */
    public function match(RequestInterface $request)
    {
        if (!$this->helper->isAllProductsEnabled()) {
            return false;
        }

        $identifier = rtrim(
            trim($request->getPathInfo(), '/'),
            $this->helper->getCatalogSeoSuffix()
        );

        if ($this->checkMatchExpressions($request, $identifier)) {
            $request->setModuleName('amshopby')
                ->setControllerName('index')
                ->setActionName('index')
                ->setAlias(
                    \Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS,
                    $identifier
                );

            return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
        }

        return false;
    }

    /**
     * @param RequestInterface $request
     * @param string $identifier
     * @return bool
     */
    public function checkMatchExpressions(RequestInterface $request, $identifier)
    {
        return $identifier == $this->helper->getAllProductsUrlKey();
    }
}
