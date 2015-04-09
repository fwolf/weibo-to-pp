<?php
namespace Fwolf\Bin\WeiboToPp;

use Fwlib\Config\GlobalConfig;
use Fwolf\Client\Coding\Coding as CodingClient;

/**
 * Poster
 *
 * @copyright   Copyright 2015 Fwolf
 * @license     http://opensource.org/licenses/MIT MIT
 */
class Poster
{
    /**
     * @var CodingClient
     */
    protected $codingClient = null;


    /**
     * @return  CodingClient
     */
    protected function createCodingClient()
    {
        return new CodingClient();
    }


    /**
     * @return  CodingClient
     */
    protected function getCodingClient()
    {
        if (is_null($this->codingClient)) {
            $config = GlobalConfig::getInstance();
            $cookieFile = $config->get('weiboToPp.cookieFile');
            $user = $config->get('weiboToPp.username');
            $pass = $config->get('weiboToPp.password');

            $client = $this->createCodingClient();
            $client->setCookieFile($cookieFile)
                ->setAuthentication($user, $pass)
                ->login();

            // Reload cookie
            $client = $this->createCodingClient();
            $client->setCookieFile($cookieFile)
                ->setAuthentication($user, $pass)
                ->login();

            $this->codingClient = $client;
        }

        return $this->codingClient;
    }


    /**
     * Post to coding pp
     *
     * @param   string      $body
     * @param   string[]    $images
     */
    public function post($body, array $images)
    {
        $client = $this->getCodingClient();

        $client->sendTweet($body, $images);
    }
}
