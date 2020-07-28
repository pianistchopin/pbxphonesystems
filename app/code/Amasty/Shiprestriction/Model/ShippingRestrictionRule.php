<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Model;

/**
 * Class ShippingRestrictionRule
 */
class ShippingRestrictionRule
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var Rule[]
     */
    private $allRules;

    /**
     * @var \Amasty\Shiprestriction\Model\ResourceModel\Rule\Collection
     */
    private $rulesCollection;

    /**
     * @var \Amasty\Shiprestriction\Model\ProductRegistry
     */
    private $productRegistry;

    /**
     * @var Message\MessageBuilder
     */
    private $messageBuilder;

    /**
     * @var \Amasty\CommonRules\Model\Validator\SalesRule
     */
    private $salesRuleValidator;

    public function __construct(
        \Magento\Framework\App\State $appState,
        \Amasty\Shiprestriction\Model\ResourceModel\Rule\Collection $rulesCollection,
        \Amasty\Shiprestriction\Model\ProductRegistry $productRegistry,
        \Amasty\Shiprestriction\Model\Message\MessageBuilder $messageBuilder,
        \Amasty\CommonRules\Model\Validator\SalesRule $salesRuleValidator
    ) {
        $this->appState = $appState;
        $this->rulesCollection = $rulesCollection;
        $this->productRegistry = $productRegistry;
        $this->messageBuilder = $messageBuilder;
        $this->salesRuleValidator = $salesRuleValidator;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRestrictionRules($request)
    {
        /** @var \Magento\Quote\Model\Quote\Item[] $allItems */
        $allItems = $request->getAllItems();

        if (!$allItems) {
            return [];
        }

        $firstItem = current($allItems);
        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $firstItem->getAddress();
        $address->setItemsToValidateRestrictions($allItems);

        //multishipping optimization
        $this->prepareAllRules($address);

        /**
         * Fix for admin checkout
         *
         * UPD: Return missing address data (discount, grandtotal, etc)
         */
        if ($this->isAdmin() && $address->hasOrigData()) {
            $address->addData($address->getOrigData());
        }

        // remember old
        $subtotal = $address->getSubtotal();
        $baseSubtotal = $address->getBaseSubtotal();
        $validRules = $this->getValidRules($address, $allItems);
        // restore
        $address->setSubtotal($subtotal);
        $address->setBaseSubtotal($baseSubtotal);

        return $validRules;
    }

    /**
     * @param $address
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareAllRules($address)
    {
        if (!$this->allRules) {
            $this->allRules = $this->rulesCollection->addAddressFilter($address);

            if ($this->isAdmin()) {
                $this->allRules->addFieldToFilter('for_admin', 1);
            }

            $this->allRules = $this->rulesCollection->getItems();

            /** @var \Amasty\Shiprestriction\Model\Rule $rule */
            foreach ($this->allRules as $rule) {
                $rule->afterLoad();
            }
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param \Magento\Quote\Model\Quote\Item[] $allItems
     *
     * @return \Amasty\Shiprestriction\Model\Rule[]
     */
    protected function getValidRules($address, $allItems)
    {
        $validRules = [];
        /** @var \Amasty\Shiprestriction\Model\Rule $rule */
        foreach ($this->allRules as $rule) {
            $this->productRegistry->clearProducts();

            if ($rule->validate($address, $allItems)
                && $this->salesRuleValidator->validate($rule, $allItems)
            ) {
                // remember used products
                $newMessage = $this->messageBuilder->parseMessage(
                    $rule->getMessage(),
                    $this->productRegistry->getProducts()
                );

                $rule->setMessage($newMessage);
                $validRules[] = $rule;
            }
        }

        return $validRules;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function isAdmin()
    {
        return $this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;
    }
}
