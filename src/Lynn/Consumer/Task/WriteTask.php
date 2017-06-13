<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-9
 * Time: 下午7:46
 */

namespace Lynn\Consumer\Task;

use Lynn\Consumer\Task;

class WriteTask extends Task {

	public function mainAction() {
		$di = $this->di;
		$config = $di['config'];
		$consumer = $di['consumer'];
		$consumer->subscribe([$config->kafka->topics->lynn]);
		$this->distribute($consumer, 'write');
	}

	protected function dealConsumer($params) {
		$dealParams = json_decode($params);
		$type = $dealParams->type;
		$func = $dealParams->func;
		$dealFunc = substr($func, 0, strlen($func) - 6);
		$reflector = new \ReflectionClass('Lynn\Common\Enum\ServiceEnum');
		$constans = $reflector->getConstants();
		$enumName = array_search($type, $constans);
		if (!$enumName) {
			return;
		} else {
			call_user_func(array($this->di[$type . 'Service'], $dealFunc), $dealParams->params);
		}
	}
}