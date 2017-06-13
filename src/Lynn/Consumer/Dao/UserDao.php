<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-9
 * Time: 下午7:29
 */

namespace Lynn\Consumer\Dao;

use Lynn\Common\Model\User;
use Lynn\Consumer\Dao;

class UserDao extends Dao {

	public function addUser($uid, $userName, $password, $nickName) {
		$user = new User();
		$user->uid = $uid;
		$user->user_name = $userName;
		$user->password = $password;
		$user->nick_name = $nickName;
		$user->create_time = date('Y-m-d H:i:s');
		$user->update_time = date('Y-m-d H:i:s');
		if (!$user->save()) {
			$this->recordMissInfo(__CLASS__, __FUNCTION__, func_get_args());
		}
		return $user;
	}
}