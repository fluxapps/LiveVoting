<?php

namespace srag\RemovePluginDataConfirm\LiveVoting;

use ilException;

/**
 * Class RemovePluginDataConfirmException
 *
 * @package srag\RemovePluginDataConfirm\LiveVoting
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class RemovePluginDataConfirmException extends ilException {

	/**
	 * RemovePluginDataConfirmException constructor
	 *
	 * @param string $message
	 * @param int    $code
	 *
	 * @internal
	 */
	public function __construct(/*string*/
		$message, /*int*/
		$code = 0) {
		parent::__construct($message, $code);
	}
}
