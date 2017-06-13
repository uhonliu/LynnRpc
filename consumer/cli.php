#!/opt/webserver/php/bin/php -q
<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-9
 * Time: 下午2:21
 */

use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Logger\Adapter\File as PhLoggerFile;
use Phalcon\Logger\Formatter\Line as PhLoggerFormatter;

define('CURRENT_DIR', dirname(__FILE__));
define('BASE_DIR', realpath(CURRENT_DIR . '/../'));

require BASE_DIR . '/vendor/autoload.php';

date_default_timezone_set('Asia/Shanghai');

$di = new CliDI();

$config = include CURRENT_DIR . '/config.php';
$di->set('config', $config);

foreach ($config->logger->filenames as $key => $filename) {
	$di->setShared($key . 'Logger', function () use ($config, $filename) {
		$logger = new PhLoggerFile(BASE_DIR . '/' . $config->logger->path . $filename);
		$formatter = new PhLoggerFormatter($config->logger->format);
		$logger->setFormatter($formatter);
		return $logger;
	});
}

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

$tasks = array('WriteTask');
foreach ($tasks as $taskName) {
	$task = 'Lynn\\Consumer\\Task\\' . $taskName;
	$di->set($taskName, function () use ($task) {
		return new $task();
	}, true);
}

$daos = array('UserDao');
foreach ($daos as $daoName) {
	$dao = 'Lynn\\Consumer\\Dao\\' . $daoName;
	$di->set($daoName, function () use ($dao) {
		return new $dao();
	}, true);
}

$services = array('SynchronizeService', 'UserService');
foreach ($services as $serviceName) {
	$service = 'Lynn\\Consumer\\Service\\' . $serviceName;
	$di->set($serviceName, function () use ($service) {
		return new $service();
	}, true);
}

$caches = array('DefaultCache');
foreach ($caches as $cacheName) {
	$cache = 'Lynn\\Common\\Cache\\' . $cacheName;
	$di->set($cacheName, function () use ($cache) {
		return new $cache();
	}, true);
}

$conf = new RdKafka\Conf();
$conf->setRebalanceCb(function (RdKafka\KafkaConsumer $kafka, $err, array $partitions = null) use ($di) {
	switch ($err) {
	case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
		$di['errorLogger']->info('Assign: ' . var_export($partitions, true));
		$kafka->assign($partitions);
		break;
	case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
		$di['errorLogger']->info('Revoke: ' . var_export($partitions, true));
		$kafka->assign(NULL);
		break;
	default:
		$di['errorLogger']->error(var_export($err));
		sleep(5);
		break;
	}
});

$conf->set('group.id', $config->kafka->groups->group);
$conf->set('metadata.broker.list', $config->kafka->brokers);

$topicConf = new RdKafka\TopicConf();
$topicConf->set('auto.offset.reset', 'smallest');
$conf->setDefaultTopicConf($topicConf);
$consumer = new RdKafka\KafkaConsumer($conf);
$di->set('consumer', $consumer);

$console = new ConsoleApp();
$console->setDI($di);

$arguments = array();
foreach ($argv as $k => $arg) {
	if ($k == 1) {
		$arguments['task'] = $arg;
	} elseif ($k == 2) {
		$arguments['action'] = $arg;
	} elseif ($k >= 3) {
		$arguments['params'][] = $arg;
	}
}

define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

try {
	$console->handle($arguments);
} catch (\Phalcon\Exception $e) {
	echo $e->getMessage();
	exit(255);
}