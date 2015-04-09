<?php
namespace FwolfTest\Bin\WeiboToPp;

use Fwolf\Bin\WeiboToPp\Poster;
use Fwolf\Client\Coding\Coding as CodingClient;
use Fwolf\Wrapper\PHPUnit\PHPUnitTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @copyright   Copyright 2015 Fwolf
 * @license     http://opensource.org/licenses/MIT MIT
 */
class PosterTest extends PHPUnitTestCase
{
    /**
     * @param   string[]    $methods
     * @return  MockObject|Poster
     */
    protected function buildMock(array $methods = null)
    {
        $mock = $this->getMock(
            Poster::class,
            $methods
        );

        return $mock;
    }


    public function testCreateCodingClient()
    {
        $poster = $this->buildMock();

        $this->assertInstanceOf(
            CodingClient::class,
            $this->reflectionCall($poster, 'createCodingClient')
        );
    }


    public function testGetCodingClient()
    {
        $codingClient = $this->getMock(
            CodingClient::class,
            ['setCookieFile', 'setAuthentication', 'login']
        );
        $codingClient->expects($this->exactly(2))
            ->method('setCookieFile')
            ->willReturnSelf();
        $codingClient->expects($this->exactly(2))
            ->method('setAuthentication')
            ->willReturnSelf();
        $codingClient->expects($this->exactly(2))
            ->method('login');

        $poster = $this->buildMock(['createCodingClient']);
        $poster->expects($this->exactly(2))
            ->method('createCodingClient')
            ->willReturn($codingClient);

        $this->assertNull($this->reflectionGet($poster, 'codingClient'));

        $this->reflectionCall($poster, 'getCodingClient');
        $this->assertNotNull($this->reflectionGet($poster, 'codingClient'));
    }


    public function testPost()
    {
        $codingClient = $this->getMock(
            CodingClient::class,
            ['sendTweet']
        );
        $codingClient->expects($this->once())
            ->method('sendTweet');

        /** @var MockObject|Poster $poster */
        $poster = $this->buildMock(['getCodingClient']);
        $poster->expects($this->once())
            ->method('getCodingClient')
            ->willReturn($codingClient);

        $poster->post('body', []);
    }
}
