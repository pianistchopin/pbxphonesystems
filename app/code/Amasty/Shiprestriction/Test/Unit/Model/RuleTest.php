<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Test\Unit\Model;

use Amasty\Shiprestriction\Model\Rule;
use Amasty\Shiprestriction\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class RuleTest
 *
 * @see Rule
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class RuleTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var Rule
     */
    private $model;

    protected function setUp()
    {
        $this->model = $this->createPartialMock(Rule::class, []);
    }

    /**
     * @covers Rule::prepareForEdit
     */
    public function testPrepareForEdit()
    {
        $this->model->setData('stores', 12);
        $this->model->setData('cust_groups', [15]);
        $this->model->setCarriers('test');
        $this->model->prepareForEdit();
        $this->assertEquals(['test', ''], $this->model->getMethods());
    }

    /**
     * @covers Rule::getStores
     */
    public function testGetStores()
    {
        $this->model->setData('stores', '1, 2, 3');
        $this->assertEquals([1, 2, 3], $this->model->getStores());
    }

    /**
     * @covers Rule::setStores
     */
    public function testSetStores()
    {
        $this->model->setStores([1, 2, 3]);
        $this->assertEquals('1,2,3', $this->model->getData('stores'));
    }
}