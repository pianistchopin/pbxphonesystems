<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Test\Unit\Controller\Adminhtml\Rule;

use Amasty\Shiprestriction\Controller\Adminhtml\Rule\Save;
use Amasty\Shiprestriction\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class SaveTest
 *
 * @see Save
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class SaveTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Save::prepareData
     */
    public function testPrepareData()
    {
        $data = ['rule_id' => 1, 'rule' => ['conditions' => 5]];
        $controller = $this->getObjectManager()->getObject(Save::class);
        $this->invokeMethod($controller, 'prepareData', [&$data]);
        $this->assertEquals(['conditions' => 5], $data);
    }
}
