<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace Amasty\Rules\Model\Rule\Validator;

use Amasty\Rules\Api\ExtendedValidatorInterface;
use Amasty\Rules\Helper\Data;
use Amasty\Rules\Model\RuleResolver;
use Magento\Quote\Model\Quote\Item;
use Magento\SalesRule\Model\Rule;

class CheckActionItemValidator implements ExtendedValidatorInterface
{
    /**
     * @var Data
     */
    private $rulesDataHelper;

    /**
     * @var RuleResolver
     */
    private $ruleResolver;

    public function __construct(
        Data $rulesDataHelper,
        RuleResolver $ruleResolver
    ) {
        $this->rulesDataHelper = $rulesDataHelper;
        $this->ruleResolver = $ruleResolver;
    }

    /**
     * @param $combineCondition
     * @param $type
     *
     * @return bool|null
     */
    public function validate($combineCondition, $type)
    {
        if ($type instanceof Item) {
            $rule = $combineCondition->getRule();
            $discountItem = $this->checkActionItem($rule, $type);
            if ($discountItem) {
                return true;
            }
        }

        return null;
    }

    /**
     * @param Rule $rule
     * @param Item $item
     *
     * @return bool
     *
     */
    protected function checkActionItem($rule, $item)
    {
        $action = $rule->getSimpleAction();

        if (strpos($action, "buyxget") !== false) {
            $this->ruleResolver->getSpecialPromotions($rule);

            $promoCats = $this->rulesDataHelper->getRuleCats($rule);
            $promoSku  = $this->rulesDataHelper->getRuleSkus($rule);
            $itemSku   = $item->getSku();
            $itemCats  = $item->getCategoryIds();

            if (!$itemCats) {
                $itemCats = $item->getProduct()->getCategoryIds();
            }

            $parent = $item->getParentItem();

            if ($parent) {
                $parentType = $parent->getProductType();
                if ($parentType == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                    $itemSku  = $item->getParentItem()->getProduct()->getSku();
                    $itemCats = $item->getParentItem()->getProduct()->getCategoryIds();
                }
            }

            if (in_array($itemSku, $promoSku)) {
                return true;
            }

            if ($itemCats !== null && array_intersect($promoCats, $itemCats)) {
                return true;
            }
        }

        return false;
    }
}
