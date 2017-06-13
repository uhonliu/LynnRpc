<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-9
 * Time: 下午6:11
 */

namespace Lynn\Producer\Controller;

use Lynn\Common\Enum\ServiceEnum;
use Lynn\Producer\Controller;

class listController extends Controller {

	/**
	 *  同步使用,可以在获取数据等场景使用
	 */

	public function synchronizeAction($params) {
		return $this->di['SynchronizeService']->synchronize($params);
	}

	/**
	 *  异步使用,可以在更新数据等场景使用
	 */

	public function addUserAction(array $params) {
		$serviceType = ServiceEnum::USER;
		return $this->asynchronousWriteLibrary($serviceType, __FUNCTION__, $params);
	}
}