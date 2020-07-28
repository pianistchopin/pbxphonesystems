<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Test\Unit\Model;

use Amasty\CommonRules\Model\Rule;
use Amasty\CommonRules\Test\Unit\Traits;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class RuleTest
 *
 * @see Rule
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @codingStandardsIgnoreFile
 */
class RuleTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;

    /**
     * @covers Rule::match
     *
     * @dataProvider getTestData
     *
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $rate
     * @param string $carriers
     * @param string $methods
     * @param bool $expectedResult
     */
    public function testMatch($rate, $carriers, $methods, $expectedResult)
    {
        /** @var MockObject|Rule $model */
        $model = $this->createPartialMock(Rule::class, []);
        $model->setCarriers($carriers);
        $model->setMethods($methods);

        $result = $model->match($rate);

        $this->assertEquals($expectedResult, $result);
    }

    public function getTestData()
    {
        return [
            [$this->getRate('carrier_1', 'method_1'), '', '', false],
            [$this->getRate('carrier_1', 'method_1'), 'carrier_1', '', true],
            [$this->getRate('carrier_1', 'method_1'), 'carrier_2,carrier_3', '', false],
            [$this->getRate('carrier_1', 'method_1'), 'carrier_2,carrier_3', 'carrier_1_method_1', true],
            [$this->getRate('carrier_1', 'method_1'), '', 'carrier_1_method_1', true],
            [$this->getRate('carrier_1', 'method_1'), '', 'carrier_1_method_2', false],
        ];
    }

    /**
     * @param string $carrierName
     * @param string $methodName
     *
     * @return MockObject|\Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function getRate($carrierName, $methodName)
    {
        /** @var MockObject|\Magento\Quote\Model\Quote\Address\RateResult\Method $rate */
        $rate = $this->createPartialMock(
            \Magento\Quote\Model\Quote\Address\RateResult\Method::class,
            ['getCarrier', 'getMethod']
        );
        $rate->expects($this->atLeastOnce())->method('getCarrier')->willReturn($carrierName);
        $rate->expects($this->any())->method('getMethod')->willReturn($methodName);

        return $rate;
    }
}
