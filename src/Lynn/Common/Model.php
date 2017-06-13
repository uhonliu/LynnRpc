<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-11
 * Time: 下午5:09
 */

namespace Lynn\Common;

class Model extends \Phalcon\Mvc\Model {

	public function initialize() {
		$this->setReadConnectionService('db_r');
		$this->setWriteConnectionService('db_w');
	}
}