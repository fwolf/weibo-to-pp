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
