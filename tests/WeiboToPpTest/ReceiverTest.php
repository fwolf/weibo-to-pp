<?php
namespace FwolfTest\Bin\WeiboToPp;

use Fwlib\Net\Curl;
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
     * @param   string[]    $methods
     * @return  MockObject|Receiver
     */
    protected function buildMock(array $methods = null)
    {
        $mock = $this->getMock(
            Receiver::class,
            $methods
        );

        return $mock;
    }


    public function test()
    {
        $receiver = $this->buildMock();

        $curl = $this->reflectionCall($receiver, 'getCurl');
        $this->assertInstanceOf(Curl::class, $curl);

        $this->reflectionCall(
            $receiver,
            'getCurlInfo',
            [$curl->getHandle(), CURLINFO_HEADER_SIZE]
        );
    }


    public function testGetImages()
    {
        $receiver = $this->buildMock(['getImagesFromGmail']);

        $receiver->expects($this->once())
            ->method('getImagesFromGmail')
            ->willReturn([]);

        $this->assertEqualArray([], $receiver->getImages());
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


    public function testGetImagesFromIfttt()
    {
        $receiver = $this->buildMock(['getLocationHeader']);
        $receiver->expects($this->exactly(4))
            ->method('getLocationHeader')
            ->willReturnOnConsecutiveCalls(
                '',
                '',
                '',
                'http://domain.tld/image.jpg'
            );

        $this->reflectionSet(
            $receiver,
            'contents',
            [
                'body-plain' => '<img src="http://domain.tld/image.jpg" style="max-width:100%;"><br>
<img src="http://domain.tld/1.jpg" style="max-width:100%;">
'
            ]
        );

        $this->assertEqualArray(
            [],
            $this->reflectionCall($receiver, 'getImagesFromIfttt')
        );
        $this->assertEqualArray(
            ['http://domain.tld/image.jpg'],
            $this->reflectionCall($receiver, 'getImagesFromIfttt')
        );
    }


    public function testGetLocationHeader()
    {
        $header = <<<TAG
HTTP/1.1 301 Moved Permanently
Date: Fri, 10 Apr 2015 07:14:42 GMT
Content-Type: text/html; charset=utf-8
Content-Length: 151
Connection: keep-alive
Cache-Control: private, max-age=90
Location: http://domain.tld/image.jpg
Mime-Version: 1.0
TAG;

        $curl = $this->getMock(Curl::class, ['get']);
        $curl->expects($this->any())
            ->method('get')
            ->willReturnOnConsecutiveCalls('', $header);

        $receiver = $this->buildMock(['getCurl', 'getCurlInfo']);
        $receiver->expects($this->any())
            ->method('getCurl')
            ->willReturn($curl);
        $receiver->expects($this->exactly(2))
            ->method('getCurlInfo')
            ->willReturnOnConsecutiveCalls(0, 300);

        $this->assertEquals(
            '',
            $this->reflectionCall($receiver, 'getLocationHeader', ['dummy'])
        );
        $this->assertEquals(
            'http://domain.tld/image.jpg',
            $this->reflectionCall($receiver, 'getLocationHeader', ['dummy'])
        );
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
