<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\UI\DataProviders\Modifiers;

use Amasty\ShippingBar\Api\Data\LabelInterface;
use Magento\Store\Model\StoreManagerInterface;

class StoreLabels
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var bool
     */
    private $haveCurrencySymbol = false;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    public function getStoreInputs($code)
    {
        $result = [];
        $sortOrderWebsite = 0;

        foreach ($this->storeManager->getWebsites() as $website) {
            $nameWebsite = 'website_' . $website->getId();
            $result[$nameWebsite] = $this->addFieldset($nameWebsite, $website->getName(), $sortOrderWebsite += 10);
            $sortOrderStoreGroups = 0;

            /** @var \Magento\Store\Model\Group $group */
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();

                if (count($stores) == 0) {
                    continue;
                }

                $sortOrderStore = 0;
                $nameStoreGroup = 'store_group_' . $group->getId();
                $result[$nameWebsite]['children'][$nameStoreGroup] =
                    $this->addFieldset($nameStoreGroup, $group->getName(), $sortOrderStoreGroups += 10);

                /** @var \Magento\Store\Model\Store $store */
                foreach ($stores as $store) {
                    if (!$this->haveCurrencySymbol) {
                        $this->haveCurrencySymbol = true;
                        $currencySymbol = $store->getCurrentCurrency()->getCurrencySymbol();
                        $result[$nameWebsite]['children'][$nameStoreGroup]['children']['currency_symbol'] =
                            $this->addHidden('currency_symbol', 1, $currencySymbol);
                    }

                    $result[$nameWebsite]['children'][$nameStoreGroup]['children']['store_' . $store->getId()] =
                        $this->addInput($store->getId(), $store->getName(), $sortOrderStore += 10, $code);
                }
            }
        }

        return $result;
    }

    private function addFieldset($name, $label, $sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'js_config' => [
                        'component' => 'Magento_Ui/js/form/components/fieldset'
                    ],
                    'config' => [
                        'componentType' => 'fieldset',
                        'collapsible' => false,
                        'label' => $label,
                        'sortOrder' => $sortOrder
                    ]
                ]
            ],
            'attributes' => [
                'class' => 'Magento\Ui\Component\Form\Fieldset',
                'name' => $name
            ],
            'children' => []
        ];
    }

    private function addInput($entityId, $label, $sortOrder, $code)
    {
        $name = 'store_' . $entityId;
        $defaultMessage = '';
        $formElement = 'input';
        $validation = true;

        switch ($code) {
            case LabelInterface::INIT_MESSAGE:
                $defaultMessage = 'Get Free Shipping for the order over {{ruleGoal}}.';
                break;
            case LabelInterface::PROGRESS_MESSAGE:
                $defaultMessage = 'Only {{ruleGoalLeft}} left for Free Shipping.';
                break;
            case LabelInterface::ACHIEVED_MESSAGE:
                $defaultMessage = 'Good news: your order will be delivered for Free.';
                break;
            case LabelInterface::TERMS_MESSAGE:
                $defaultMessage = 'Free Shipping is available for all orders over {{ruleGoal}} for USA ' .
                    'delivery addresses only.';
                $formElement = 'textarea';
                $validation = false;

                break;
        }

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'field',
                        'dataType' => 'text',
                        'formElement' => $formElement,
                        'dataScope' => 'labels[' . $code . '][' . $entityId . ']',
                        'default' => $defaultMessage,
                        'label' => $label,
                        'validation' => [
                            'required-entry' => $validation
                        ],
                        'sortOrder' => $sortOrder
                    ]
                ]
            ],
            'attributes' => [
                'class' => 'Magento\Ui\Component\Form\Field',
                'name' => $name
            ],
            'children' => []
        ];
    }

    private function addHidden($name, $sortOrder, $value)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'field',
                        'dataType' => 'text',
                        'formElement' => 'hidden',
                        'dataScope' => $name,
                        'value' => $value,
                        'sortOrder' => $sortOrder
                    ]
                ]
            ],
            'attributes' => [
                'class' => 'Magento\Ui\Component\Form\Field',
                'name' => $name
            ],
            'children' => []
        ];
    }
}
