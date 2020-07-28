<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Plugin\Search\Request;

use Amasty\ElasticSearch\Model\Config;
use Magento\Catalog\Block\Product\ProductList\Toolbar;

/**
 * Class Binder
 * @package Amasty\ElasticSearch\Plugin\Search\Request
 */
class Binder
{
    const ALL_TOOLBAR_OPTION = 'all';

    /**
     * @var Toolbar
     */
    protected $toolbar;

    /**
     * @var Config
     */
    protected $configProvider;

    public function __construct(
        Toolbar $toolbar,
        Config $configProvider
    ) {
        $this->toolbar = $toolbar;
        $this->configProvider = $configProvider;
    }

    /**
     * Fix all products toolbar option on Magento version > 2.3.2
     * @param \Magento\Framework\Search\Request\Binder $subject
     * @param array $resultData
     * @param array $requestData
     * @param array $bindData
     * @return array
     */
    public function afterBind(
        \Magento\Framework\Search\Request\Binder $subject,
        array $resultData,
        array $requestData,
        array $bindData
    ) {
        if ($this->toolbar && $this->toolbar->getLimit() === self::ALL_TOOLBAR_OPTION
            && !$resultData['size'] && $this->configProvider->isElasticEngine()
        ) {
            $resultData['size'] = $requestData['size'];
        }

        return $resultData;
    }
}
