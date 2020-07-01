<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Helper;

class VariationsForeBay
{
    /**
     * Generate matrix of variation
     *
     * @param array $usedProductAttributes
     * @return array
     */
    public function getVariations($usedProductAttributes, $arrayType = false)
    {
        $variationalAttributes = $arrayType ? $usedProductAttributes :
                            $this->combineVariationalAttributes($usedProductAttributes);
        $attributesCount = count($variationalAttributes);
        if ($attributesCount === 0) {
            return [];
        }

        $variations = [];
        $currentVariation = array_fill(0, $attributesCount, 0);
        $variationalAttributes = array_reverse($variationalAttributes);
        $lastAttribute = $attributesCount - 1;
        do {
            $this->incrementVariationalIndex($attributesCount, $variationalAttributes, $currentVariation);
            if ($currentVariation[$lastAttribute] >= count($variationalAttributes[$lastAttribute]['values'])) {
                break;
            }

            $filledVariation = [];
            for ($attributeIndex = $attributesCount; $attributeIndex--;) {
                $currentAttribute = $variationalAttributes[$attributeIndex];
                $currentVariationValue = $currentVariation[$attributeIndex];
                $filledVariation[$currentAttribute['id']] = $currentAttribute['values'][$currentVariationValue];
            }

            $variations[] = $filledVariation;
            $currentVariation[0]++;
        } while (true);

        return $variations;
    }

    /**
     * Combine variational attributes
     *
     * @param array $usedProductAttributes
     * @return array
     */
    private function combineVariationalAttributes($usedProductAttributes)
    {
        $variationalAttributes = [];
        foreach ($usedProductAttributes as $variation) {
            try {
                $options = [];
                foreach ($variation->getValues() as $optionData) {
                    if ($optionData->getDefaultTitle() != '') {
                        $optionData = $optionData->getData();
                        $optionData['variation_title'] = $variation->getDefaultTitle();
                        $options[] = $optionData;
                    }
                }
                $variationalAttributes[] = [
                    'id' => $variation->getOptionId(),
                    'values' => $options,
                    'title' => $variation->getDefaultTitle()
                ];
            } catch (\Exception $e) {
                continue;
            }
        }
        return $variationalAttributes;
    }

    /**
     * Increment index in variation with shift if overflow
     *
     * @param int $attributesCount
     * @param array $variationalAttributes
     * @param array $currentVariation
     * @return void
     */
    private function incrementVariationalIndex($attributesCount, $variationalAttributes, &$currentVariation)
    {
        for ($attributeIndex = 0; $attributeIndex < $attributesCount - 1; ++$attributeIndex) {
            if ($currentVariation[$attributeIndex] >= count($variationalAttributes[$attributeIndex]['values'])) {
                $currentVariation[$attributeIndex] = 0;
                ++$currentVariation[$attributeIndex + 1];
            }
        }
    }
}
