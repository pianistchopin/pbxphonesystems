<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\PluginFix;

/**
 * Class PluginFactory
 */
class PluginFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $instanceName;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = \Magento\Framework\DataObject::class
    ) {
        $this->objectManager = $objectManager;

        try {
            new \ReflectionClass($instanceName);
            $this->instanceName = $instanceName;
        } catch (\ReflectionException $e) {
            $this->instanceName = \Magento\Framework\DataObject::class;
        }
    }

    /**
     *
     * @param array $arguments
     * @return mixed
     * @throws \UnexpectedValueException
     */
    public function create(array $arguments = [])
    {
        return $this->objectManager->create($this->instanceName, $arguments);
    }
}
