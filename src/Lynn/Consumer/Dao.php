<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-9
 * Time: 下午7:10
 */

namespace Lynn\Consumer;

use Phalcon\DiInterface;
use Phalcon\Di\InjectionAwareInterface;

abstract class Dao implements InjectionAwareInterface {

	protected $di;

	public function setDI(DiInterface $di) {
		$this->di = $di;
	}

	public function getDI() {
		return $this->di;
	}

	protected function recordMissInfo($class, $func, $args) {

		$ReflectionFunc = new \ReflectionMethod($class, $func);

		$paramsKey = array_map(function ($item) {
			return $item->name;
		}, $ReflectionFunc->getParameters());

		$paramsArray = array_combine($paramsKey, $args);
		$annalInfo = array_merge(array('class' => $class, 'func' => $func), $paramsArray);

		$this->di['annalLogger']->log(json_encode($annalInfo));

	}
}