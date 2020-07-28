<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Plugin\ElasticSearch\Model\Search\GetRequestQuery;

use Amasty\Xsearch\Model\Config;

/**
 * Class SortingProviderPlugin
 */
class SortingProviderPlugin
{
    const FIELD = 'stock_status';
    const DIRECTION = 'desc';

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param mixed $subject
     * @param array $result
     * @return array
     */
    public function afterGetRequestedSorting($subject, array $result)
    {
        if ($this->config->isShowOutOfStockLast()) {
            array_unshift($result, ['field' => self::FIELD, 'direction' => self::DIRECTION]);
        }

        return $result;
    }
}