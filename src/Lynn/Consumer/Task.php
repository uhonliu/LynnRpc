<?php

/**
 * Created by PhpStorm.
 * User: sephiroth
 * Date: 17-6-9
 * Time: 下午6:29
 */

namespace Lynn\Consumer;

abstract class Task extends \Phalcon\Cli\Task {

	public function distribute($consumer, $serviceName) {
		while (true) {
			$message = $consumer->consume(120 * 1000);
			switch ($message->err) {
			case RD_KAFKA_RESP_ERR_NO_ERROR:
				$this->dealConsumer($message->payload);
				break;
			case RD_KAFKA_RESP_ERR__PARTITION_EOF:
				break;
			case RD_KAFKA_RESP_ERR__TIMED_OUT:
				break;
			default:
				$this->di['errorLogger']->error($message->errstr() . '-----' . $message->err);
				sleep(5);
				break;
			}
		}
	}

	abstract protected function dealConsumer($params);
}