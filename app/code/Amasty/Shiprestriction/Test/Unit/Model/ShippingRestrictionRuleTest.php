<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Test\Unit\Model;

use Amasty\Shiprestriction\Model\Message\MessageBuilder;
use Amasty\Shiprestriction\Model\ShippingRestrictionRule;
use Amasty\Shiprestriction\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ShippingRestrictionRuleTest
 *
 * @see ShippingRestrictionRule
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ShippingRestrictionRuleTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var ShippingRestrictionRule
     */
    private $model;

    protected function setUp()
    {
        $this->model = $this->createPartialMock(
            ShippingRestrictionRule::class,
            ['prepareAllRules', 'isAdmin']
        );

        $this->model->expects($this->any())->method('isAdmin')->willReturn(true);
    }

    /**
     * @covers ShippingRestrictionRule::getRestrictionRules
     */
    public function testGetRestrictionRules()
    {
        $request = $this->getObjectManager()->getObject(\Magento\Quote\Model\Quote\Address\RateRequest::class);
        $item = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $address = $this->createPartialMock(
            \Magento\Quote\Model\Quote\Address::class,
            ['setItemsToValidateRestrictions', 'setSubtotal', 'getSubtotal']
        );

        $item->expects($this->any())->method('getAddress')->willReturn($address);
        $address->expects($this->once())->method('setSubtotal')->willReturn($address);
        $address->expects($this->once())->method('getSubtotal')->willReturn($address);
        $this->model->expects($this->once())->method('isAdmin')->willReturn(true);

        $this->setProperty($this->model, 'allRules', [], ShippingRestrictionRule::class);

        $request->setAllItems(false);
        $this->assertEquals([], $this->model->getRestrictionRules($request));

        $request->setAllItems([$item]);
        $this->model->getRestrictionRules($request);
    }

    /**
     * @covers ShippingRestrictionRule::getValidRules
     */
    public function testGetValidRules()
    {
        $item = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $address = $this->createPartialMock(\Magento\Quote\Model\Quote\Address::class, []);
        $rule1 = $this->createMock(\Amasty\Shiprestriction\Model\Rule::class);
        $rule2 = $this->createMock(\Amasty\Shiprestriction\Model\Rule::class);
        $productRegistry = $this->createPartialMock(\Amasty\Shiprestriction\Model\ProductRegistry::class, []);
        $salesRuleValidator = $this->createPartialMock(
            \Amasty\CommonRules\Model\Validator\SalesRule::class,
            ['validate']
        );
        $messageBuilder = $this->createMock(MessageBuilder::class, []);

        $salesRuleValidator->expects($this->any())->method('validate')->willReturn(true);
        $rule1->expects($this->any())->method('validate')->willReturn(true);
        $rule2->expects($this->any())->method('validate')->willReturn(false);
        $messageBuilder->expects($this->exactly(2))->method('parseMessage');

        $this->setProperty($this->model, 'allRules', [$rule1, $rule2], ShippingRestrictionRule::class);
        $this->setProperty($this->model, 'productRegistry', $productRegistry, ShippingRestrictionRule::class);
        $this->setProperty($this->model, 'salesRuleValidator', $salesRuleValidator, ShippingRestrictionRule::class);
        $this->setProperty($this->model, 'messageBuilder', $messageBuilder, ShippingRestrictionRule::class);

        $this->assertArrayHasKey(0, $this->invokeMethod($this->model, 'getValidRules', [$address, [$item]]));
        $this->assertArrayNotHasKey(1, $this->invokeMethod($this->model, 'getValidRules', [$address, [$item]]));
    }
}
