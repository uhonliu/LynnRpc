<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-13
 * Time: 上午10:21
 */

define('BASE_DIR', realpath(dirname(__FILE__) . '/../'));

require BASE_DIR . '/vendor/autoload.php';

$client = new \Hprose\Http\Client('http://127.0.0.1:9601', false);
$result = $client->synchronizeAction('lynn');

echo $result . "\n";
