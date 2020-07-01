<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class ProductImportType extends AbstractSource
{

    /**
     * Return options array.
     * @param int $store
     * @return array
     */
    public function toOptionArray($store = null)
    {

            $importType =[
                 [
                    'value' => 1,
                    'label' => __('All products')
                 ],
                 [
                    'value' => 0,
                    'label' => __('Only mapped categories\'s product')
                 ]
            ];
            return $importType;
    }

    /**
     * Get options in "key-value" format.
     * @return array
     */
    public function toArray()
    {
        $optionList = $this->toOptionArray();
        $optionArray = [];
        foreach ($optionList as $option) {
            $optionArray[$option['value']] = $option['label'];
        }
        return $optionArray;
    }

    /**
     * Return options array.
     * @param int $store
     * @return array
     */
    public function getAllOptions($store = null)
    {
        return $this->toOptionArray();
    }

    /**
     * Get a text for option value.
     * @param string|int $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
