<?php
namespace FwolfTest\Bin\WeiboToPp;

use Fwlib\Util\Common\HttpUtil;
use Fwlib\Util\UtilContainer;
use Fwolf\Bin\WeiboToPp\Receiver;
use Fwolf\Wrapper\PHPUnit\PHPUnitTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 *
 * @copyright   Copyright 2015 Fwolf
 * @license     http://opensource.org/licenses/MIT MIT
 */
class ReceiverTest extends PHPUnitTestCase
{
    /**
     * @return MockObject|Receiver
     */
    protected function buildMock()
    {
        $mock = $this->getMock(
            Receiver::class,
            null
        );

        return $mock;
    }


    public function testGetImages()
    {
        $receiver = $this->buildMock();

        $_FILES = [];
        $this->assertEmpty($receiver->getImages());

        $this->reflectionSet($receiver, 'contents', ['attachment-count' => 2]);
        $_FILES = [
            'attachment-1' => [
                'name' => 'local-filename.png',
                'type' => 'image/png',
                'tmp_name' => '/tmp/1',
                'error' => 0,
                'size' => 2635,
            ],
            'attachment-2' => [
                'name' => 'local-filename.png',
                'type' => 'image/png',
                'tmp_name' => '/tmp/2',
                'error' => 0,
                'size' => 2635,
            ],
        ];

        $this->assertEqualArray(
            ['/tmp/1', '/tmp/2'],
            $receiver->getImages()
        );

        $_FILES = [];
    }


    public function testReceive()
    {
        $utilContainer = UtilContainer::getInstance();
        $httpUtilBackup = $utilContainer->getHttp();

        $posts = ['subject' => 'foo'];
        $httpUtil = $this->getMock(HttpUtil::class, ['getPosts']);
        $httpUtil->expects($this->any())
            ->method('getPosts')
            ->willReturn($posts);
        $utilContainer->register('Http', $httpUtil);

        $receiver = $this->buildMock();
        $receiver->receive();
        $this->assertEquals('foo', $receiver->getBody());

        $utilContainer->register('Http', $httpUtilBackup);
    }
}
