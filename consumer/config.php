<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-9
 * Time: 下午2:22
 */

use Phalcon\Config;

$setting = array(
	'database' => array(
		'w' => array(
			'adapter' => 'mysql',
			'host' => '127.0.0.1',
			'port' => 3306,
			'username' => 'lynn',
			'password' => '123456',
			'dbname' => 'lynn',
		),
		'r' => array(
			'adapter' => 'mysql',
			'host' => '127.0.0.1',
			'port' => 3306,
			'username' => 'lynn',
			'password' => '123456',
			'dbname' => 'lynn',
		),
	),
	'redis' => array(
		'default' => array(
			'host' => '127.0.0.1',
			'port' => 6379,
			'index' => 0,
			'auth' => '123456',
		),
	),
	'kafka' => array(
		'brokers' => '127.0.0.1:9092',
		'topics' => array(
			'lynn' => 'lynn',
		),
		'groups' => array(
			'group' => 'rpc',
		),
	),
	'logger' => array(
		'adaptor' => 'File',
		'path' => 'data/log/',
		'filenames' => array(
			'debug' => 'consumer_debug.log',
			'error' => 'consumer_error.log',
		),
		'format' => '[%date%][%type%] %message%',
	),
);

$extConfigFile = CURRENT_DIR . "/config.ext.php";
if (file_exists($extConfigFile)) {
	$extConfig = include $extConfigFile;
	$setting = array_merge($setting, $extConfig);
}

return new Config($setting);