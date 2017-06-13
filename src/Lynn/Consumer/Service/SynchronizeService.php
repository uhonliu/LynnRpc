<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-12
 * Time: 下午7:54
 */

namespace Lynn\Consumer\Service;

use Lynn\Consumer\Service;

class SynchronizeService extends Service {

	public function synchronize($params) {
		return "Hello, $params, Is's crazy synchronize!";
	}

}