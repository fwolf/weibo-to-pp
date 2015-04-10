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


    public function testGetImagesFromGmail()
    {
        $receiver = $this->buildMock();

        $_FILES = [];
        $this->assertEmpty($receiver->getImagesFromGmail());

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
            $receiver->getImagesFromGmail()
        );

        $_FILES = [];
    }


    public function testIsMailFromIfttt()
    {
        $receiver = $this->buildMock();

        $this->reflectionSet(
            $receiver,
            'contents',
            ['from' => 'somebody@gmail.com']
        );
        $this->assertFalse(
            $this->reflectionCall($receiver, 'isMailFromIfttt')
        );

        $this->reflectionSet(
            $receiver,
            'contents',
            ['from' => 'IFTTT Action <action@ifttt.com>']
        );
        $this->assertTrue(
            $this->reflectionCall($receiver, 'isMailFromIfttt')
        );
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
