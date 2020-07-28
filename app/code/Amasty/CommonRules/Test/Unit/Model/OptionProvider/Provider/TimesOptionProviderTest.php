<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Test\Unit\Model\OptionProvider\Provider;

use Amasty\CommonRules\Model\OptionProvider\Provider\TimesOptionProvider;
use Amasty\CommonRules\Test\Unit\Traits;

/**
 * Class TimesOptionProviderTest
 *
 * @see TimesOptionProvider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @codingStandardsIgnoreFile
 */
class TimesOptionProviderTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;

    /**
     * @covers TimesOptionProvider::toOptionArray
     */
    public function testToOptionArray()
    {
        /** @var TimesOptionProvider $model */
        $model = $this->getObjectManager()->getObject(TimesOptionProvider::class);
        $result = $model->toOptionArray();

        $this->assertInstanceOf(\Magento\Framework\Phrase::class, current($result)['label']);
        $this->assertCount(24 * 60 / 15 + 1, $result);
        $this->assertEquals(2346, end($result)['value']);

        $sameResult = $model->toOptionArray();
        $this->assertEquals($result, $sameResult);
    }
}
