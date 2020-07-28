<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */


namespace Amasty\Pgrid\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Model\Product;

/**
 * Class Price
 */
class TierPrice extends Column
{
    protected $product;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Product $product,
        array $components = [],
        array $data = []
    ) {
        $this->product = $product;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $tierPriceString = '';
                $product = $this->product->load($item['entity_id']);
                if (!$product->getData('tier_price')) {
                    return $dataSource;
                }
                $productTierPrices = $product->getTierPrices();
                foreach ($productTierPrices as $tierPriceItem) {
                    if ((int)$tierPriceItem['qty'] != 0 && (int)$tierPriceItem['value'] != 0) {
                        $tierPriceString .= '<p style="width:130px;">' .
                            __('For Qty') . ' = ' . round($tierPriceItem['qty'], 2) .
                            __(' Price') . ' = ' . round($tierPriceItem['value'], 2) .'</p>';
                    }
                }
                $item['amasty_tier_price'] = $tierPriceString;
            }
        }

        return $dataSource;
    }
}
