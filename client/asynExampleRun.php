<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-13
 * Time: 上午11:59
 */

define('BASE_DIR', realpath(dirname(__FILE__) . '/../'));

require BASE_DIR . '/vendor/autoload.php';

$client = new \Hprose\Http\Client('http://127.0.0.1:9601', false);
$uid = md5(uniqid(mt_rand(), true));
$result = $client->addUserAction(array('uid' => $uid, 'user_name' => 'tenyears', 'password' => 123456, 'nick_name' => '阿楼'));

echo $result . "\n";
