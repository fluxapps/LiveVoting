<?php

namespace srag\DIC\Exception;

use ilException;

/**
 * Class DICException
 *
 * @package srag\DIC\Exception
 */
final class DICException extends ilException {

	/**
	 * DICException constructor
	 *
	 * @param string $message
	 * @param int    $code
	 */
	public function __construct($message, $code = 0) {
		parent::__construct($message, $code);
	}
}
