<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Ui\Component\Listing\Column;

class Date extends \Magento\Ui\Component\Listing\Columns\Date
{
    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (!$item[$this->getData('name')]) {
                    $item[$this->getData('name')] = __('N/A');
                }
            }
        }

        return $dataSource;
    }
}
