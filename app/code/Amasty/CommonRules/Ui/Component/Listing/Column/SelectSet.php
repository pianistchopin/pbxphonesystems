<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class SelectSet
 */
class SelectSet extends Column
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $options = $this->getData('config/options');
        $key = $this->getName();

        $emptyValue = $this->getData('config/emptyValue');

        foreach ($dataSource['data']['items'] as &$item) {
            $newValue = [];
            if (!$item[$key]) {
                $item[$key] = $emptyValue;
                continue;
            }
            if (is_string($item[$key])) {
                $item[$key] = explode(',', $item[$key]);
            }
            foreach ($options as $option) {
                if (in_array($option['value'], $item[$key])) {
                    $newValue[] = $option['label'];
                }
            }
            if (empty($newValue)) {
                $item[$key] = $emptyValue;
            } else {
                $item[$key] = implode('<br/>', $newValue); // @codingStandardsIgnoreLine
            }
        }

        return $dataSource;
    }
}
