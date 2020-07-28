<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Indexer\Structure;

use Amasty\ElasticSearch\Api\Data\Indexer\Structure\AnalyzerBuilderInterface;
use Amasty\ElasticSearch\Model\Source\CustomAnalyzer;

/**
 * Class AnalyserBuilder
 */
class AnalyserBuilderFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $data
     * @return AnalyzerBuilderInterface
     */
    public function create($type)
    {
        return $this->objectManager->create($this->getBuilderInstanceName($type));
    }

    /**
     * @param string $type
     * @return string
     */
    private function getBuilderInstanceName($type)
    {
        switch ($type) {
            case CustomAnalyzer::CHINESE:
                $name = \Amasty\ElasticSearch\Model\Indexer\Structure\AnalyzerBuilder\Smartcn::class;
                break;
            case CustomAnalyzer::JAPANESE:
                $name = \Amasty\ElasticSearch\Model\Indexer\Structure\AnalyzerBuilder\Kuromoji::class;
                break;
            case CustomAnalyzer::KOREAN:
                $name = \Amasty\ElasticSearch\Model\Indexer\Structure\AnalyzerBuilder\Nori::class;
                break;
            case CustomAnalyzer::DISABLED:
            default:
                $name = \Amasty\ElasticSearch\Model\Indexer\Structure\AnalyzerBuilder\DefaultBuilder::class;
                break;
        }
        return $name;
    }
}
