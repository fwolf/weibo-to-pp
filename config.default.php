<?php
/**
 * Default configure file
 *
 * For user customized configure, copy this file as 'config.php', and change
 * value in it, or remove unchanged value. These two files will be automatic
 * loaded.
 *
 * In this file, there can use $userConfig to reference user customized config
 * value, but remember to give default value if user config not set.
 *
 * @copyright   Copyright 2015 Fwolf
 * @license     http://opensource.org/licenses/MIT MIT
 */

// Used by curl to store login status, should be writable by web server
$config['weiboToPp.cookieFile'] = '/tmp/coding.txt';

// Only post weibo have this hash tag, without '#', leave empty to post all
$config['weiboToPp.hashTag'] = 'coding';

$config['weiboToPp.username'] = '';
$config['weiboToPp.password'] = '';

return $config;
