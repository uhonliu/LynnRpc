<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-13
 * Time: 下午3:01
 */

namespace Lynn\Common\Model;

use Lynn\Common\Model;

class User extends Model {

	/**
	 *
	 * @var string
	 */
	public $uid;

	/**
	 *
	 * @var string
	 */
	public $user_name;

	/**
	 *
	 * @var string
	 */
	public $password;

	/**
	 *
	 * @var string
	 */
	public $nick_name;

	/**
	 *
	 * @var string
	 */
	public $create_time;

	/**
	 *
	 * @var string
	 */
	public $update_time;

	public function getSource() {
		return 'user';
	}

	public static function find($parameters = null) {
		return parent::find($parameters);
	}

	public static function findFirst($parameters = null) {
		return parent::findFirst($parameters);
	}
}