<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\PluginFix;

/**
 * Class EmptyPlugin
 */
class EmptyPlugin
{
    /**
     * @var array
     */
    private $methodArray = [
        'before',
        'after',
        'around'
    ];

    /**
     * @var mixed
     */
    private $instance;

    public function __construct(\Amasty\ElasticSearch\PluginFix\PluginFactory $instanceFactory)
    {
        $this->instance = $instanceFactory->create();
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __call($method, $args)
    {
        if (method_exists($this->instance, $method)) {
            // phpcs:ignore
            return call_user_func([$this->instance, $method], $args);
        }

        foreach ($this->methodArray as $methodPrefix) {
            if (strpos($method, $methodPrefix) === 0) {
                break;
            }
            $methodPrefix = null;
        }

        switch ($methodPrefix) {
            case 'before':
                $args = array_slice($args, 1);
                return $args;
            case 'around':
                $proceed = $args[1];
                return $proceed(array_slice($args, 2));
            case 'after':
                $result = $args[1];
                return $args[1];
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            new \Magento\Framework\Phrase('Invalid method %1::%2', [get_class($this->instance), $method])
        );
    }
}
