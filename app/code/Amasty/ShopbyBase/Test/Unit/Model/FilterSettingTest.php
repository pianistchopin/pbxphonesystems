<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Test\Unit\Model;

use Amasty\ShopbyBase\Model\FilterSetting;
use Amasty\ShopbyBase\Model\FilterSettingFactory;
use Amasty\ShopbyBase\Test\Unit\Traits;

/**
 * Class FilterSettingTest
 *
 * @see FilterSetting
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class FilterSettingTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ReflectionTrait;
    use Traits\ObjectManagerTrait;

    /**
     * @var FilterSetting
     */
    private $model;

    /**
     * @var \Amasty\ShopbyBase\Model\FilterSettingFactory
     */
    private $filterSettingFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    private $attrModel;

    /**
     * @var FilterSettingFactory
     */
    private $filterSettings;

    public function setUp()
    {
        $this->model = $this->getObjectManager()->getObject(FilterSetting::class);
        $this->filterSettingFactory = $this
            ->createPartialMock(\Amasty\ShopbyBase\Model\FilterSettingFactory::class, ['create']);
        $this->attrModel = $this->createMock(\Magento\Eav\Model\Entity\Attribute::class);
        $this->filterSettings = $this->getMockBuilder(FilterSettingFactory::class)
            ->setMethods(['getGroupsByAttributeId'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers FilterSettingTest::getAttributeGroups
     *
     * @throws \ReflectionException
     */
    public function testGetAttributeGroups()
    {
        $result = $this->model->getAttributeGroups();
        $this->assertEmpty($result);

        $this->filterSettingFactory->expects($this->any())->method('create')->willReturnCallback(
            function () {
                return $this->filterSettings;
            }
        );
        $this->setProperty($this->model, 'groupAttrDataProviderFactory', $this->filterSettingFactory);
        $this->attrModel->expects($this->any())->method('getId')->willReturn(1);
        $this->filterSettings->expects($this->any())->method('getGroupsByAttributeId')->willReturn(['test']);
        $this->model->setData('attribute_model', $this->attrModel);

        $result = $this->model->getAttributeGroups();
        $this->assertNotEmpty($result);
    }

    /**
     * @covers FilterSettingTest::getIndexMode
     *
     * @throws \ReflectionException
     */
    public function testGetIndexMode()
    {
        $this->assertEquals(0, $this->model->getIndexMode());
        $this->model->setData($this->model::INDEX_MODE, 'test_index_mode');
        $this->assertEquals('test_index_mode', $this->model->getIndexMode());
    }

    /**
     * @covers FilterSettingTest::getUnitsLabel
     *
     * @throws \ReflectionException
     */
    public function testGetUnitsLabel()
    {
        $this->assertEquals(null, $this->model->getUnitsLabel());
        $this->model->setData($this->model::USE_CURRENCY_SYMBOL, 'test');
        $this->assertEquals('test', $this->model->getUnitsLabel('test'));
    }
}
