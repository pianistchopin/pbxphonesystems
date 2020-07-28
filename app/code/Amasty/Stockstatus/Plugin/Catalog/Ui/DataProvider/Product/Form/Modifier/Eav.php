<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav as NativeModifier;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Amasty\Stockstatus\Model\Backend\UpdaterAttribute;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Eav
{
    const PRODUCT_DETAILS_AREA = 'product-details';

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var NativeModifier
     */
    private $modifier;

    function __construct(
        LocatorInterface $locator,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->locator = $locator;
    }

    /**
     * @param NativeModifier $modifier
     * @param array $meta
     *
     * @return array
     */
    public function afterModifyMeta($modifier, $meta)
    {
        $this->modifier = $modifier;

        $this->addQtyBasedMeta($meta);
        $this->updateRuleAttrMeta($meta);

        return $meta;
    }

    /**
     * @param $attributeCode
     *
     * @return AttributeInterface|null
     */
    private function getAttribute($attributeCode)
    {
        try {
            $attribute = $this->attributeRepository->get(Product::ENTITY, $attributeCode);
        } catch (NoSuchEntityException $entityException) {
            $attribute = null;
        }

        return $attribute;
    }

    /**
     * @return AttributeInterface|null
     */
    private function getQtyBasedAttr()
    {
        return $this->getAttribute('custom_stock_status_qty_based');
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function generateSwitcher($config)
    {
        $switcherConfig = ['enabled' => true, 'rules' => []];

        foreach ($config as $value => $actions) {
            $switcherConfig['rules'][] = [
                'value'   => $value,
                'actions' => $actions
            ];
        }

        return $switcherConfig;

    }

    /**
     * @param array $meta
     */
    private function addQtyBasedMeta(&$meta)
    {
        if (($attribute = $this->getQtyBasedAttr())
            && isset(
                $meta[self::PRODUCT_DETAILS_AREA]['children'][NativeModifier::CONTAINER_PREFIX
                . 'custom_stock_status']['children']['custom_stock_status']['arguments']['data']['config']
            )
        ) {
            $stockStatusConfig = $meta[self::PRODUCT_DETAILS_AREA]['children'][NativeModifier::CONTAINER_PREFIX
            . 'custom_stock_status']['children']['custom_stock_status']['arguments']['data']['config'];

            // show qty based attr
            $meta[self::PRODUCT_DETAILS_AREA]['children'][NativeModifier::CONTAINER_PREFIX
            . 'custom_stock_status_qty_based'] = $this->createAttributeContainer(
                $attribute
            );

            $meta[self::PRODUCT_DETAILS_AREA]['children'][NativeModifier::CONTAINER_PREFIX
            . 'custom_stock_status_qty_based']['children']['custom_stock_status_qty_based']['arguments']['data']['config']['sortOrder'] =
                $stockStatusConfig['sortOrder'] + 1;
            $meta[self::PRODUCT_DETAILS_AREA]['children'][NativeModifier::CONTAINER_PREFIX
            . 'custom_stock_status_qty_based']['arguments']['data']['config']['sortOrder'] =
                $stockStatusConfig['sortOrder'] + 1;
        }
    }

    /**
     * @param array $meta
     */
    private function updateRuleAttrMeta(&$meta)
    {
        if (isset(
                $meta[self::PRODUCT_DETAILS_AREA]['children'][NativeModifier::CONTAINER_PREFIX
                . UpdaterAttribute::QTY_RULE_CODE]['children'][UpdaterAttribute::QTY_RULE_CODE]['arguments']['data']['config']
            )
            && isset(
                $meta[self::PRODUCT_DETAILS_AREA]['children'][NativeModifier::CONTAINER_PREFIX
                . 'custom_stock_status_qty_based']['children']['custom_stock_status_qty_based']['arguments']['data']['config']
            )
        ) {
            $qtyBasedAttrConfig = &$meta[self::PRODUCT_DETAILS_AREA]['children'][NativeModifier::CONTAINER_PREFIX
            . 'custom_stock_status_qty_based']['children']['custom_stock_status_qty_based']['arguments']['data']['config'];

            // put qty rule attr after qtyBasedAttr

            $qtyRuleAttr = &$meta[self::PRODUCT_DETAILS_AREA]['children'][NativeModifier::CONTAINER_PREFIX
            . UpdaterAttribute::QTY_RULE_CODE];
            $qtyRuleAttr['children'][UpdaterAttribute::QTY_RULE_CODE]['arguments']['data']['config']['sortOrder'] =
                $qtyBasedAttrConfig['sortOrder'] + 1;
            $qtyRuleAttr['arguments']['data']['config']['sortOrder'] =
                $qtyBasedAttrConfig['sortOrder'] + 1;

            // add switcher for qty rule attr
            $qtyRuleSource = sprintf(
                'product_form.product_form.product-details.container_%s.%s',
                UpdaterAttribute::QTY_RULE_CODE,
                UpdaterAttribute::QTY_RULE_CODE
            );

            $qtyBasedAttrConfig['switcherConfig'] =
                $this->generateSwitcher(
                    [
                        0 => [
                            [
                                'target'   => $qtyRuleSource,
                                'callback' => 'hide'
                            ]
                        ],
                        1 => [
                            [
                                'target'   => $qtyRuleSource,
                                'callback' => 'show'
                            ]
                        ]
                    ]
                );
        }
    }

    /**
     * @param AttributeInterface $attribute
     * @param int $sortOrder
     *
     * @return array
     */
    private function createAttributeContainer($attribute, $sortOrder = 0)
    {
        $attributeContainer = $this->modifier->setupAttributeContainerMeta($attribute);
        $attributeContainer =
            $this->modifier->addContainerChildren(
                $attributeContainer,
                $attribute,
                self::PRODUCT_DETAILS_AREA,
                $sortOrder
            );

        return $attributeContainer;
    }

    /**
     * @param NativeModifier $modifier
     * @param array $data
     *
     * @return array
     */
    public function afterModifyData(NativeModifier $modifier, $data)
    {
        $attribute = $this->getQtyBasedAttr();
        $productId = $this->locator->getProduct()->getId();

        $data[$productId][NativeModifier::DATA_SOURCE_DEFAULT][$attribute->getAttributeCode()] = $modifier->setupAttributeData($attribute);

        return $data;
    }
}
