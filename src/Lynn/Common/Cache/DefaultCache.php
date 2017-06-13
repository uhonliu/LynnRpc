<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-10
 * Time: 下午2:00
 */

namespace Lynn\Common\Cache;

use Lynn\Common\Cache;

class DefaultCache extends Cache {

	public function __construct() {
		$this->_db = 'redis_default';
	}
}