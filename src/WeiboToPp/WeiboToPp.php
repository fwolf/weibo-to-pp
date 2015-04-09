<?php
namespace Fwolf\Bin\WeiboToPp;

use Fwlib\Config\GlobalConfig;

/**
 * WeiboToPp, main entrance
 *
 * @copyright   Copyright 2015 Fwolf
 * @license     http://opensource.org/licenses/MIT MIT
 */
class WeiboToPp
{
    /**
     * @return  Poster
     */
    protected function createPoster()
    {
        return new Poster();
    }


    /**
     * @return  Receiver
     */
    protected function createReceiver()
    {
        return new Receiver();
    }


    /**
     * Do some modify before post body
     *
     * @param   string  $body
     * @return  string
     */
    protected function decorate($body)
    {
        $hashTag = GlobalConfig::getInstance()->get('weiboToPp.hashTag');

        if (!empty($hashTag)) {
            $body = preg_replace("/#{$hashTag}[ #]/i", '', $body);
        }

        return $body;
    }


    /**
     * Is message suit for send to pp ?
     *
     * @param   string      $body
     * @param   string[]    $images
     * @return  bool
     */
    protected function isSuitable($body, array $images)
    {
        if (empty($body) && empty($images)) {
            return false;
        }

        $hashTag = GlobalConfig::getInstance()->get('weiboToPp.hashTag');
        if (!empty($hashTag) && 1 !== preg_match("/#{$hashTag}[ #]/i", $body)) {
            return false;
        }

        return true;
    }


    /**
     * Main function
     */
    public function main()
    {
        $receiver = $this->createReceiver()->receive();
        $body = $receiver->getBody();
        $images = $receiver->getImages();

        if ($this->isSuitable($body, $images)) {
            $body = $this->decorate($body);

            $poster = $this->createPoster();
            $poster->post($body, $images);
        }
    }
}
