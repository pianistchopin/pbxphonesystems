<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Ui\DataProvider;

use Amasty\CommonRules\Model\MethodConverter;
use Amasty\CommonRules\Model\ResourceModel\Rule\Collection as CommonRulesCollection;

/**
 * @method CommonRulesCollection getCollection()
 */
class AbstractDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const METHODS_FIELD = 'methods';

    /**
     * @var MethodConverter
     */
    protected $converter;

    /**
     * @var CommonRulesCollection
     */
    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CommonRulesCollection $collection,
        MethodConverter $converter,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collection;
        $this->converter = $converter;
    }

    public function getData()
    {
        $data = parent::getData();

        if (empty($data['totalRecords'])) {
            return $data;
        }

        foreach ($data['items'] as &$item) {
            $item[self::METHODS_FIELD] = $this->converter->convert($item[self::METHODS_FIELD]);
            $item[self::METHODS_FIELD] = $item[self::METHODS_FIELD] ?: __('Any');
        }

        return $data;
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     *
     * @return CommonRulesCollection
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $collection = $this->getCollection();
        switch ($filter->getField()) {
            case self::METHODS_FIELD:
                $collection->addMethodFilter($this->converter->getCodes($filter->getValue()));
                break;
            case 'carriers':
                $collection->addCarriersFilter([$filter->getValue()]);
                break;
            case 'stores':
                $collection->addStoreFilter($filter->getValue());
                break;
            case 'cust_groups':
                $collection->addCustomerGroupFilter($filter->getValue());
                break;
            default:
                parent::addFilter($filter);
        }

        return $collection;
    }
}
