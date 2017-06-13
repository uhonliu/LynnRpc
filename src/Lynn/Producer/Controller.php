<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-9
 * Time: 下午9:07
 */

namespace Lynn\Producer;

use Exception;
use Phalcon\DiInterface;
use Phalcon\Di\InjectionAwareInterface;

abstract class Controller implements InjectionAwareInterface {

	protected $di;

	public function setDI(DiInterface $di) {
		$this->di = $di;
	}

	public function getDI() {
		return $this->di;
	}

	protected function asynchronousWriteLibrary($type, $func, $params) {
		try {
			$topic = $this->di['lynnTopic'];
			$params = array("type" => $type, "func" => $func, "params" => $params);
			$topic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($params, JSON_UNESCAPED_UNICODE));
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
}
