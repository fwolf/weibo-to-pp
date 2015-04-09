<?php
namespace FwolfTest\Bin\WeiboToPp;

use Fwlib\Config\GlobalConfig;
use Fwolf\Bin\WeiboToPp\Poster;
use Fwolf\Bin\WeiboToPp\Receiver;
use Fwolf\Bin\WeiboToPp\WeiboToPp;
use Fwolf\Wrapper\PHPUnit\PHPUnitTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @copyright   Copyright 2015 Fwolf
 * @license     http://opensource.org/licenses/MIT MIT
 */
class WeiboToPpTest extends PHPUnitTestCase
{
    /** @var array */
    protected static $hashTagConfigBackup = [];


    /**
     * @param   string[]    $methods
     * @return  MockObject|WeiboToPp
     */
    protected function buildMock(array $methods = null)
    {
        $mock = $this->getMock(
            WeiboToPp::class,
            $methods
        );

        return $mock;
    }


    public static function setUpBeforeClass()
    {
        $config = GlobalConfig::getInstance();
        self::$hashTagConfigBackup = $config->get('weiboToPp.hashTag');
    }


    public static function tearDownAfterClass()
    {
        $config = GlobalConfig::getInstance();
        $config->set('weiboToPp.hashTag', self::$hashTagConfigBackup);
    }


    public function test()
    {
        $weiboToPp = $this->buildMock();

        $this->assertInstanceOf(
            Poster::class,
            $this->reflectionCall($weiboToPp, 'createPoster')
        );

        $this->assertInstanceOf(
            Receiver::class,
            $this->reflectionCall($weiboToPp, 'createReceiver')
        );
    }


    public function testDecorate()
    {
        $weiboToPp = $this->buildMock();

        $config = GlobalConfig::getInstance();
        $config->set('weiboToPp.hashTag', 'coding');

        $this->assertEquals(
            '',
            $this->reflectionCall($weiboToPp, 'decorate', [''])
        );
        $this->assertEquals(
            'foo bar',
            $this->reflectionCall($weiboToPp, 'decorate', ['foo bar'])
        );
        $this->assertEquals(
            'foo bar',
            $this->reflectionCall($weiboToPp, 'decorate', ['foo #coding bar'])
        );
        $this->assertEquals(
            'foo  bar',
            $this->reflectionCall($weiboToPp, 'decorate', ['foo #coding# bar'])
        );

        $config->set('weiboToPp.hashTag', '');
        $this->assertEquals(
            'foo #bar',
            $this->reflectionCall($weiboToPp, 'decorate', ['foo #bar'])
        );
    }


    public function testIsSuitable()
    {
        $weiboToPp = $this->buildMock();

        $config = GlobalConfig::getInstance();
        $config->set('weiboToPp.hashTag', 'coding');

        $this->assertFalse(
            $this->reflectionCall($weiboToPp, 'isSuitable', ['', []])
        );
        // Empty body does not match hash tag
        $this->assertFalse(
            $this->reflectionCall($weiboToPp, 'isSuitable', ['', ['img path']])
        );
        $this->assertFalse(
            $this->reflectionCall($weiboToPp, 'isSuitable', ['foo bar', []])
        );
        $this->assertFalse(
            $this->reflectionCall($weiboToPp, 'isSuitable', ['foo #codingbar', []])
        );
        $this->assertTrue(
            $this->reflectionCall($weiboToPp, 'isSuitable', ['foo #coding bar', []])
        );
        $this->assertTrue(
            $this->reflectionCall($weiboToPp, 'isSuitable', ['foo #coding#', []])
        );

        $config->set('weiboToPp.hashTag', '');
        $this->assertTrue(
            $this->reflectionCall($weiboToPp, 'isSuitable', ['foo bar', []])
        );
        $this->assertTrue(
            $this->reflectionCall($weiboToPp, 'isSuitable', ['', ['img path']])
        );
    }


    public function testMain()
    {
        $weiboToPp = $this->buildMock(
            ['createPoster', 'createReceiver', 'isSuitable']
        );

        $poster = $this->getMock(Poster::class, []);
        $weiboToPp->expects($this->once())
            ->method('createPoster')
            ->willReturn($poster);

        $receiver = $this->getMock(Receiver::class, ['getBody', 'getImages']);
        $receiver->expects($this->once())
            ->method('getBody')
            ->willReturn('body');
        $receiver->expects($this->once())
            ->method('getImages')
            ->willReturn([]);
        $weiboToPp->expects($this->once())
            ->method('createReceiver')
            ->willReturn($receiver);

        $weiboToPp->expects($this->once())
            ->method('isSuitable')
            ->willReturn(true);

        $weiboToPp->main();
    }
}
