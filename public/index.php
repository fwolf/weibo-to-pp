<?php
use Fwolf\Bin\WeiboToPp\WeiboToPp;

require __DIR__ . '/../bootstrap.php';

$client = new WeiboToPp();
$client->main();
