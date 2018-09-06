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
	 *
	 * @access namespace
	 */
	public function __construct(/*string*/
		$message, /*int*/
		$code = 0) {
		parent::__construct($message, $code);
	}
}
