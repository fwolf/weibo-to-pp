<?php
namespace Fwolf\Bin\WeiboToPp;

use Fwlib\Util\UtilContainerAwareTrait;

/**
 * Receiver
 *
 * @copyright   Copyright 2015 Fwolf
 * @license     http://opensource.org/licenses/MIT MIT
 */
class Receiver
{
    use UtilContainerAwareTrait;


    /**
     * Raw posted content
     *
     * @var array
     */
    protected $contents = [];


    /**
     * Get weibo body
     *
     * @return  string
     */
    public function getBody()
    {
        return $this->contents['subject'];
    }


    /**
     * Receive posted mail from mailgun
     *
     * @return  static
     */
    public function receive()
    {
        $httpUtil = $this->getUtilContainer()->getHttp();

        $this->contents = $httpUtil->getPosts();

        return $this;
    }
}
