<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */


namespace Amasty\Pgrid\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;


class Availability extends Column implements OptionSourceInterface
{
    const DISABLE_MANAGE_STOCK = 2;
    const IN_STOCK = 1;
    const OUT_OF_STOCK = 0;

    /**
     * @var StockConfigurationInterface
     */
    protected $stockConfiguration;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StockConfigurationInterface $stockConfiguration,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->stockConfiguration = $stockConfiguration;
    }

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::DISABLE_MANAGE_STOCK => __('Manage Stock Disabled'),
            self::IN_STOCK => __('In Stock'),
            self::OUT_OF_STOCK => __('Out Of Stock')
        ];
    }

    /**
     * @return array
     */
    public static function getAllOptions()
    {
        $res = [];

        foreach (self::getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }

        return $res;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }


    /**
     * Getting expression for available column
     *
     * @return string
     */
    public function getAvailableExpression()
    {
        $configManageStock = $this->stockConfiguration->getManageStock();

        return 'IF(((at_amasty_availability.manage_stock = 0 AND at_amasty_availability.use_config_manage_stock = 0)' .
            ' OR (' . $configManageStock . ' = 0 AND at_amasty_availability.use_config_manage_stock = 1)),'
            . self::DISABLE_MANAGE_STOCK . ', at_amasty_availability.is_in_stock)';

    }
}
