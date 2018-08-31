<?php

namespace srag\RemovePluginDataConfirm;

use ilException;

/**
 * Class RemovePluginDataConfirmException
 *
 * @package srag\RemovePluginDataConfirm
 */
class RemovePluginDataConfirmException extends ilException {

	/**
	 * RemovePluginDataConfirmException constructor
	 *
	 * @param string $message
	 * @param int    $code
	 */
	public function __construct($message, $code = 0) {
		parent::__construct($message, $code);
	}
}
