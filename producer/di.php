<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-9
 * Time: 下午2:19
 */

use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Di\FactoryDefault;
use Phalcon\Logger\Adapter\File as LoggerAdapter;
use Phalcon\Logger\Formatter\Line as LoggerFormatter;
use Phalcon\Mvc\Model\Metadata\Files;
use RdKafka\Producer;

$di = new FactoryDefault();
$config = require CURRENT_DIR . '/config.php';
$di->setShared('config', $config);

foreach ($config->logger->filenames as $key => $filename) {
	$di->setShared($key . 'Logger', function () use ($config, $filename) {
		$logger = new LoggerAdapter(BASE_DIR . '/' . $config->logger->path . $filename);
		$formatter = new LoggerFormatter($config->logger->format);
		$logger->setFormatter($formatter);
		return $logger;
	});
}

$di->set('db_r', function () use ($di) {
	$config = $di->get('config');
	$connection = new DbAdapter(array(
		'host' => $config->database->r->host,
		'port' => $config->database->r->port,
		'username' => $config->database->r->username,
		'password' => $config->database->r->password,
		'dbname' => $config->database->r->dbname,
		"options" => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
		),
	));
	return $connection;
}, true);

$di->set('db_w', function () use ($di) {
	$config = $di->get('config');
	$connection = new DbAdapter(array(
		'host' => $config->database->w->host,
		'port' => $config->database->w->port,
		'username' => $config->database->w->username,
		'password' => $config->database->w->password,
		'dbname' => $config->database->w->dbname,
		"options" => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
		),
	));
	return $connection;
}, true);

$di->set('modelsMetadata', function () {
	$metaData = new Files([
		'metaDataDir' => BASE_DIR . '/data/metaData/',
	]);
	return $metaData;
});

foreach ($config->redis as $schema_name => $db_config) {
	$servName = 'redis_' . $schema_name;
	$di->setShared($servName, function () use ($db_config) {
		$redis = new Redis();
		$redis->connect($db_config->host, $db_config->port);
		isset($db_config->auth) && $redis->auth($db_config->auth);
		$redis->select($db_config->index);
		return $redis;
	});
}

foreach ($config->kafka->topics as $key => $topicName) {
	$di->setShared($key . 'Topic', function () use ($config, $topicName) {
		$rkProducer = new Producer();
		$rkProducer->setLogLevel(LOG_DEBUG);
		$brokers = $config->kafka->brokers;
		$rkProducer->addBrokers($brokers);
		$topic = $rkProducer->newTopic($topicName);
		return $topic;
	});
}

$caches = array('DefaultCache');
foreach ($caches as $cacheName) {
	$cache = 'Lynn\\Common\\Cache\\' . $cacheName;
	$di->set($cacheName, function () use ($cache) {
		return new $cache();
	}, true);
}

$controllers = array('listController');
foreach ($controllers as $controllerName) {
	$controller = 'Lynn\\Producer\\Controller\\' . $controllerName;
	$di->set($controllerName, function () use ($controller) {
		return new $controller();
	}, true);
}

$services = array('SynchronizeService');
foreach ($services as $serviceName) {
	$service = 'Lynn\\Consumer\\Service\\' . $serviceName;
	$di->set($serviceName, function () use ($service) {
		return new $service();
	}, true);
}

return $di;