<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Test\Unit\Model\Validator;

use Amasty\CommonRules\Model\Rule as CommonRule;
use Amasty\CommonRules\Model\Validator\SalesRule;
use Amasty\CommonRules\Test\Unit\Traits;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class SalesRuleTest
 *
 * @see SalesRule
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @codingStandardsIgnoreFile
 */
class SalesRuleTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers SalesRule::getCouponCodes
     *
     * @dataProvider getCouponsTestData
     *
     * @param array $items
     * @param array $expectedResult
     *
     * @throws \ReflectionException
     */
    public function testGetCouponCodes($items, $expectedResult)
    {
        $model = $this->getObjectManager()->getObject(SalesRule::class);
        $result = $this->invokeMethod($model, 'getCouponCodes', [$items]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @covers SalesRule::getCouponCodes
     *
     * @dataProvider getRulesTestData
     *
     * @param array $items
     * @param array $expectedResult
     *
     * @throws \ReflectionException
     */
    public function testGetRuleIds($items, $expectedResult)
    {
        $model = $this->getObjectManager()->getObject(SalesRule::class);
        $result = $this->invokeMethod($model, 'getRuleIds', [$items]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @covers SalesRule::isApply
     *
     * @dataProvider getApplyData
     *
     * @param CommonRule $rule
     * @param array $providedCouponCodes
     * @param array $providedRuleIds
     * @param bool $isDisable
     * @param bool $expectedResult
     *
     * @throws \ReflectionException
     */
    public function testIsApply($rule, $providedCouponCodes, $providedRuleIds, $isDisable, $expectedResult)
    {
        $model = $this->getObjectManager()->getObject(SalesRule::class);
        $result = $this->invokeMethod($model, 'isApply', [$rule, $providedCouponCodes, $providedRuleIds, $isDisable]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * DataProvider
     *
     * @return array
     */
    public function getCouponsTestData()
    {
        return [
            [$this->getItemsArray(), []],
            [$this->getItemsArray('coupon_1,coupon_2'), ['coupon_1', 'coupon_2']],
            [$this->getItemsArray(' coupon_1, coupon_2 '), ['coupon_1', 'coupon_2']]
        ];
    }

    /**
     * DataProvider
     *
     * @return array
     */
    public function getRulesTestData()
    {
        return [
            [$this->getItemsArray(), []],
            [$this->getItemsArray('', '1,2,11'), ['1', '2', '11']],
            [$this->getItemsArray('', '1'), ['1']]
        ];
    }

    /**
     * DataProvider
     *
     * @return array
     */
    public function getApplyData()
    {
        return [
            [
                $this->getRule('', '', true),
                [],
                [],
                true,
                true
            ], // 0
            [
                $this->getRule('1,ALFA', '', true),
                ['ALFA'],
                [],
                true,
                true
            ], // 1
            [
                $this->getRule('ALFA', '', true),
                ['1,ALFA'],
                [],
                true,
                true
            ], // 2
            [
                $this->getRule('', '1,7,2', true),
                [],
                ['1'],
                true,
                false
            ], // 3
            [
                $this->getRule('', '1,7,2', true),
                [],
                ['1', '7', '2'],
                true,
                false
            ], // 4
            [
                $this->getRule('1', '', true),
                ['1'],
                [],
                true,
                false
            ], // 5
            [
                $this->getRule('1,coupon', '1,190', true),
                ['1', 'coupon'],
                ['1'],
                true,
                false
            ], // 6
            [
                $this->getRule('1, coupon', '1,190', false),
                ['1', 'coupon'],
                ['1', '190'],
                false,
                false
            ], // 7
            [
                $this->getRule('', '', false),
                [],
                [],
                false,
                false
            ], // 8
            [
                $this->getRule('1,ALFA', '', false),
                ['ALFA'],
                [],
                false,
                true
            ], // 9
            [
                $this->getRule('ALFA', '', false),
                ['1,ALFA'],
                [],
                false,
                true
            ], // 10
            [
                $this->getRule('', '1,7,2', false),
                [],
                ['1'],
                false,
                false
            ], // 11
            [
                $this->getRule('', '1,7,2', false),
                [],
                ['1', '7', '2'],
                false,
                false
            ], // 12
            [
                $this->getRule('1', '', false),
                ['1'],
                [],
                false,
                false
            ], // 13
        ];
    }

    /**
     * @param string $couponsString
     * @param string $rulesIdString
     *
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    private function getItemsArray($couponsString = '', $rulesIdString = '')
    {
        /** @var \Magento\Quote\Model\Quote|MockObject $quote */
        $quote = $this->createPartialMock(\Magento\Quote\Model\Quote::class, []);
        $quote
            ->setStoreId(1)
            ->setCouponCode($couponsString)
            ->setAppliedRuleIds($rulesIdString);

        /** @var \Magento\Quote\Model\Quote\Item|MockObject $item */
        $item = $this->createPartialMock(\Magento\Quote\Model\Quote\Item::class, []);
        $item->setQuote($quote);

        return [$item];
    }

    /**
     * @param string $coupons
     * @param string $ruleIds
     * @param bool $disable
     *
     * @return CommonRule|MockObject
     */
    private function getRule($coupons, $ruleIds, $disable)
    {
        /** @var CommonRule|MockObject $rule */
        $rule = $this->createPartialMock(
            CommonRule::class,
            ['getCoupon', 'getCouponDisable', 'getDiscountId', 'getDiscountIdDisable']
        );
        $rule->expects($disable ? $this->once() : $this->never())->method('getCouponDisable')->willReturn($coupons);
        $rule->expects($disable ? $this->never() : $this->once())->method('getCoupon')->willReturn($coupons);
        $rule->expects($disable ? $this->once() : $this->never())->method('getDiscountIdDisable')->willReturn($ruleIds);
        $rule->expects($disable ? $this->never() : $this->once())->method('getDiscountId')->willReturn($ruleIds);

        return $rule;
    }
}
