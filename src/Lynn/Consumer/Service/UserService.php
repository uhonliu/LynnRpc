<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-9
 * Time: 下午7:54
 */

namespace Lynn\Consumer\Service;

use Lynn\Consumer\Service;

class UserService extends Service {

	public function addUser($params) {
		$uid = $params->uid;
		$userName = $params->user_name;
		$password = $params->password;
		$nickName = $params->nick_name;
		$this->di['UserDao']->addUser($uid, $userName, md5($password), $nickName);
	}

}