<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\UI\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class Goal extends Column
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

        $key = $this->getName();

        foreach ($dataSource['data']['items'] as &$item) {
            if ($item[\Amasty\ShippingBar\Api\Data\ProfileInterface::GOAL_SOURCE] != 0) {
                $item[$key] = __('{Free Shipping Amount}');
            }
        }

        return $dataSource;
    }
}
