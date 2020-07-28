<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Test\Unit\Block\Adminhtml\System\Config;

use Amasty\Shiprestriction\Block\Adminhtml\System\Config\Tooltip;
use Amasty\Shiprestriction\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class TooltipTest
 *
 * @see Tooltip
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class TooltipTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Save::_renderValue
     */
    public function testRenderValue()
    {
        $result = '<td class="value with-tooltip">test<div '
            . 'data-bind="{\'tooltip\':{\'trigger\':\'[data-tooltip-trigger=trigger]\',\'action\':\'click\','
            . '\'delay\':0,\'track\':false,\'position\':\'top\'}}" class="hidden">tooltip</div><div '
            . 'class="tooltip" data-tooltip-trigger="trigger"><span class="help"><span></span></div>'
            . '<p class="note"><span>comment</span></p></td>';
        $block = $this->createPartialMock(Tooltip::class, ['_getElementHtml']);
        $element = $this->getObjectManager()->getObject(\Magento\Framework\Data\Form\Element\Fieldset::class);

        $block->expects($this->any())->method('_getElementHtml')->willReturn('test');

        $this->assertEquals('<td class="value">test</td>', $this->invokeMethod($block, '_renderValue', [$element]));

        $element->setTooltip('tooltip');
        $element->setComment('comment');
        $this->assertEquals($result, $this->invokeMethod($block, '_renderValue', [$element]));
    }
}
