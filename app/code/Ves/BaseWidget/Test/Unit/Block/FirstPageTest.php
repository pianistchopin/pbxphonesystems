<?php
/***
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ves\BaseWidget\Test\Unit\Block;

class FirstPageTest extends \PHPUnit_Framework_TestCase
{
    public function testGetNextPageUrl()
    {
        $nextUrl = 'magento.com/url/to/next/page';
        $inputData = ['url' => $nextUrl];

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $model = $objectManager->getObject('Ves\BaseWidget\Block\FirstPage', ['data' => $inputData]);
        $this->assertSame($nextUrl, $model->getNextPageUrl());
    }
}
