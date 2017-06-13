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

	protected function recordMissInfo($func, $args) {

	}
}