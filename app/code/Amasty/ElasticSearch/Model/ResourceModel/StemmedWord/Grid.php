<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


/**
 * @codingStandardsIgnoreFile
 */
namespace Amasty\ElasticSearch\Model\ResourceModel\StemmedWord;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Amasty\ElasticSearch\Api\Data\StemmedWordInterface;
use Amasty\ElasticSearch\Model\StemmedWord;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * Class Grid
 * @package Amasty\ElasticSearch\Model\ResourceModel\StemmedWord
 */
class Grid extends SearchResult
{
    protected $document = StemmedWord::class;

    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = StemmedWordInterface::TABLE_NAME,
        $resourceModel = \Amasty\ElasticSearch\Model\ResourceModel\StemmedWord::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }
}
