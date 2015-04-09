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
     * Do some modify before post body
     *
     * @param   string  $body
     * @return  string
     */
    protected function decorate($body)
    {
        $hashTag = GlobalConfig::getInstance()->get('weiboToPp.hashTag');

        if (!empty($hashTag)) {
            $body = preg_replace("/#{$hashTag}[ #]/", '', $body);
        }

        return $body;
    }


    /**
     * Is message suit for send to pp ?
     *
     * @param   string  $body
     * @return  bool
     */
    protected function isSuitable($body)
    {
        if (empty($body)) {
            return false;
        }

        $hashTag = GlobalConfig::getInstance()->get('weiboToPp.hashTag');
        if (!empty($hashTag) && 1 !== preg_match("/#{$hashTag}[ #]/", $body)) {
            return false;
        }

        return true;
    }
}
