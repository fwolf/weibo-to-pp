<?php
namespace FwolfTest\Bin\WeiboToPp;

use Fwlib\Config\GlobalConfig;
use Fwolf\Bin\WeiboToPp\WeiboToPp;
use Fwolf\Wrapper\PHPUnit\PHPUnitTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @copyright   Copyright 2015 Fwolf
 * @license     http://opensource.org/licenses/MIT MIT
 */
class WeiboToPpTest extends PHPUnitTestCase
{
    /**
     * @return MockObject|WeiboToPp
     */
    protected function buildMock()
    {
        $mock = $this->getMock(
            WeiboToPp::class,
            null
        );

        return $mock;
    }


    public function testIsSuitable()
    {
        $weiboToPp = $this->buildMock();

        $config = GlobalConfig::getInstance();
        $hashTagBackup = $config->get('weiboToPp.hashTag');
        $config->set('weiboToPp.hashTag', 'coding');

        $this->assertFalse(
            $this->reflectionCall($weiboToPp, 'isSuitable', [''])
        );
        $this->assertFalse(
            $this->reflectionCall($weiboToPp, 'isSuitable', ['foo bar'])
        );
        $this->assertFalse(
            $this->reflectionCall($weiboToPp, 'isSuitable', ['foo #codingbar'])
        );
        $this->assertTrue(
            $this->reflectionCall($weiboToPp, 'isSuitable', ['foo #coding bar'])
        );
        $this->assertTrue(
            $this->reflectionCall($weiboToPp, 'isSuitable', ['foo #coding#'])
        );

        $config->set('weiboToPp.hashTag', $hashTagBackup);
    }
}
