<?php
/**
 * Bootstrap
 *
 *  - Register ClassLoader
 *  - Load default and user config
 *
 * @copyright   Copyright 2015 Fwolf
 * @license     http://opensource.org/licenses/MIT MIT
 */

use Fwlib\Config\GlobalConfig;

$classLoader = require __DIR__ . '/vendor/autoload.php';


$configs = require __DIR__ . '/config.default.php';
if (file_exists(__DIR__ . '/config.php')) {
    /** @noinspection PhpIncludeInspection */
    $userConfigs = require __DIR__ . '/config.php';
    $configs = array_merge($configs, $userConfigs);
}

GlobalConfig::getInstance()->load($configs);
