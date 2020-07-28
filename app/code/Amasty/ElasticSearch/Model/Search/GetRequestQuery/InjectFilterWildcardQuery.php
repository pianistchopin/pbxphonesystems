<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Search\GetRequestQuery;

use Magento\Framework\Search\Request\QueryInterface;
use Amasty\ElasticSearch\Model\Search\GetRequestQuery\GetAggregations\FieldMapper;

/**
 * Class InjectFilterWildcardQuery
 */
class InjectFilterWildcardQuery implements InjectSubqueryInterface
{
    /**
     * @var FieldMapper
     */
    private $fieldMapper;

    public function __construct(FieldMapper $fieldMapper)
    {
        $this->fieldMapper = $fieldMapper;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $elasticQuery, QueryInterface $request, $conditionType)
    {
        /** @var \Magento\Framework\Search\Request\Filter\Term $filter */
        $filter = $request->getReference();
        $fieldName = $this->fieldMapper->mapFieldName($filter->getField());
        if (!isset($elasticQuery['bool'][$conditionType])) {
            $elasticQuery['bool'][$conditionType] = [];
        }

        $value = str_replace('/', '\/', preg_quote($filter->getValue()));

        $elasticQuery['bool'][$conditionType][] = [
            'wildcard' => [
                $fieldName => '*' . $value . '*'
            ]
        ];

        return $elasticQuery;
    }
}
