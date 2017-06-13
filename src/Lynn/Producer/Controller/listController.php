<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-9
 * Time: 下午6:11
 */

namespace Lynn\Producer\Controller;

use Lynn\Producer\Controller;

class listController extends Controller {

	public function synchronizeAction($params) {
		return $this->di['SynchronizeService']->synchronize($params);
	}
}