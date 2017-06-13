<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-11
 * Time: 下午5:01
 */

namespace Lynn\Common;

use Phalcon\DiInterface;
use Phalcon\Di\InjectionAwareInterface;

abstract class Cache implements InjectionAwareInterface {

	protected $di;
	protected $_db;

	public function setDI(DiInterface $di) {
		$this->di = $di;
	}

	public function getDI() {
		return $this->di;
	}

	public function incr($key) {
		$redis = $this->di->get($this->_db);
		$redis->incr($key);
	}

	public function decr($key) {
		$redis = $this->di->get($this->_db);
		$redis->decr($key);
	}

	public function get($key) {
		$redis = $this->di->get($this->_db);
		$value = $redis->get($key);
		return unserialize($value);
	}

	public function getCallback($key, $callback, $lifetime = 60) {
		$redis = $this->di->get($this->_db);
		$value = $this->get($key);
		if (empty($value)) {
			$value = call_user_func($callback);
			$this->set($key, $value, $lifetime);
		}
		return $value;
	}

	public function set($key, $value, $lifetime = 60) {
		if (is_null($value)) {
			return;
		}
		$redis = $this->di->get($this->_db);
		$redis->set($key, serialize($value), $lifetime);
	}

	public function delete($key) {
		$redis = $this->di->get($this->_db);
		$redis->delete($key);
	}

	public function lenth($key) {
		$redis = $this->di->get($this->_db);
		return $redis->llen($key);
	}

	public function lPushSome($key, ...$value) {
		$redis = $this->di->get($this->_db);
		$redis->lpush($key, ...$value);
	}

	public function lPushWithLimit($key, $value, $limit) {
		$redis = $this->di->get($this->_db);
		$lenth = $redis->lpush($key, $value);
		if ($lenth > $limit) {
			$redis->rpop($key);
		}
	}

	public function lRange($key, $begin, $end) {
		$redis = $this->di->get($this->_db);
		$value = $redis->lrange($key, $begin, $end);
		return $value;
	}

	public function hLen($hashKey) {
		$redis = $this->di->get($this->_db);
		return $redis->hLen($hashKey);
	}

	public function hashMset($hashKey, $value) {
		$redis = $this->di->get($this->_db);
		$redis->hMset($hashKey, $value);
	}

	public function hashAllValues($hashKey) {
		$redis = $this->di->get($this->_db);
		return $redis->hgetall($hashKey);
	}

	public function hashSet($hashKey, $key, $value) {
		$redis = $this->di->get($this->_db);
		$redis->hSet($hashKey, $key, $value);
	}

	public function hashGet($hashKey, $key) {
		$redis = $this->di->get($this->_db);
		return $redis->hGet($hashKey, $key);
	}

	public function hGetCallback($hashKey, $key, $callback) {
		$redis = $this->di->get($this->_db);
		$value = $this->hashGet($hashKey, $key);
		if (empty($value)) {
			$value = call_user_func($callback);
			if (is_null($value)) {
				return null;
			}
			$this->hashSet($hashKey, $key, $value);
		}
		return $value;
	}

	public function hGetAllCallback($hashKey, $callback) {
		$redis = $this->di->get($this->_db);
		$value = $this->hashAllValues($hashKey);
		if (empty($value)) {
			$value = call_user_func($callback);
			if (is_null($value)) {
				return null;
			}
			$this->hashMset($hashKey, $value);
		}
		return $value;
	}

	public function lPushWithLimitAndDelete($key, $value, $limit) {
		$redis = $this->di->get($this->_db);
		$lenth = $redis->lpush($key, $value);
		if ($lenth > $limit) {
			$allValues = $this->lRange($key, 0, -1);
			$oldItem = end($allValues);
			$redis->rpop($key);
			$redis->delete($oldItem);
		}
	}
}
