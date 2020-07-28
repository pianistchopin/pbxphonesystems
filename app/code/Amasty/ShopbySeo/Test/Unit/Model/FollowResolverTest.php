<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */


namespace Amasty\ShopbySeo\Test\Unit\Model;

use Amasty\ShopbyBase\Helper\FilterSetting as FilterHelper;
use Amasty\ShopbySeo\Model\FollowResolver;
use Amasty\ShopbySeo\Test\Unit\Traits;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class FollowResolverTest
 *
 * @see FollowResolver
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class FollowResolverTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var FollowResolver
     */
    private $model;

    /**
     * @var \Amasty\ShopbyBase\Helper\Data
     */
    private $baseHelper;

    /**
     * @var \Amasty\ShopbySeo\Helper\Meta
     */
    private $metaHelper;

    /**
     * @var \Amasty\Shopby\Model\Layer\Filter\Item
     */
    private $item;

    /**
     * @var FilterHelper
     */
    private $filterHelper;

    public function setUp()
    {
        $this->baseHelper = $this->createMock(\Amasty\ShopbyBase\Helper\Data::class);
        $this->metaHelper = $this->createMock(\Amasty\ShopbySeo\Helper\Meta::class);
        $this->filterHelper = $this->createMock(FilterHelper::class);

        $this->item = $this->getObjectManager()->getObject(\Amasty\Shopby\Model\Layer\Filter\Item::class);

        $this->model = $this->getObjectManager()->getObject(
            FollowResolver::class,
            [
                'baseHelper' => $this->baseHelper,
                'metaHelper' => $this->metaHelper,
                'filterHelper' => $this->filterHelper,
            ]
        );
    }

    /**
     * @covers FollowResolver::relFollow
     */
    public function testRelFollowWhenDisable()
    {
        $this->baseHelper->expects($this->any())->method('isEnableRelNofollow')->will($this->onConsecutiveCalls(true, false));
        $this->metaHelper->expects($this->any())->method('isFollowingAllowed')->will($this->onConsecutiveCalls(false, true));

        $this->assertTrue($this->model->relFollow($this->item));
        $this->assertTrue($this->model->relFollow($this->item));
    }

    /**
     * @covers FollowResolver::relFollow
     */
    public function testRelFollowWithBadSettings()
    {
        $setting = $this->getObjectManager()->getObject(\Amasty\ShopbyBase\Model\FilterSetting::class);
        $filter = $this->createMock(FilterInterface::class);
        $this->baseHelper->expects($this->any())->method('isEnableRelNofollow')->willReturn(true);
        $this->filterHelper->expects($this->any())
            ->method('getSettingByLayerFilter')
            ->will($this->onConsecutiveCalls(false, $setting));
        $this->item->setFilter($filter);

        $this->assertFalse($this->model->relFollow($this->item));

        $setting->setId(1);
        $setting->setRelNofollow(2);
        $this->assertFalse($this->model->relFollow($this->item));
    }

    /**
     * @covers FollowResolver::relFollow
     */
    public function testRelFollow()
    {
        $setting = $this->getObjectManager()->getObject(\Amasty\ShopbyBase\Model\FilterSetting::class);
        $filter = $this->createMock(FilterInterface::class);
        $this->baseHelper->expects($this->any())->method('isEnableRelNofollow')->willReturn(true);
        $this->filterHelper->expects($this->any())->method('getSettingByLayerFilter')->willReturn($setting);
        $this->item->setFilter($filter);
        $this->item->setValue(['a', 'b']);

        $this->assertTrue($this->model->relFollow($this->item));

        $setting->setData('is_multiselect', true);
        $this->assertFalse($this->model->relFollow($this->item));
    }
}
