<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-9
 * Time: 下午2:40
 */

use Hprose\Swoole\Server as Server;

define('CURRENT_DIR', dirname(__FILE__));
define('BASE_DIR', realpath(CURRENT_DIR . '/../'));

require BASE_DIR . '/vendor/autoload.php';
require CURRENT_DIR . '/di.php';

try {

	if (isset($argv[1]) && $argv[1] == '-p' && isset($argv[2])) {
		$port = $argv[2];
	} else {
		$port = 9601;
	}
	$server = new Server("http://0.0.0.0:$port");
	$server->set(
		array(
			'worker_num' => 16,
			'deamonize' => false,
			'max_request' => 10000,
			'log_file' => BASE_DIR . '/data/log/HttpServer.log',
		)
	);

	$listReflection = new ReflectionClass('Lynn\Producer\Controller\listController');
	$reflectionClass = $listReflection->getMethods(256);
	$dealMethodName = array_map(function ($itemObj) {
		if (strpos($itemObj->name, 'Action') && $itemObj->class == 'Lynn\Producer\Controller\listController') {
			return $itemObj->name;
		}
	}, $reflectionClass);
	$methodList = array_values(array_filter($dealMethodName));

	$server->addMethods($methodList, $di['listController']);
	$server->addMissingFunction(function () {return 'ah, this method is not found~';});

	$server->start();

} catch (Exception $e) {
	echo $e->getMessage() . PHP_EOL;
	echo $e->getTraceAsString() . PHP_EOL;
}