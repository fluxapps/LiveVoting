<?php

namespace LiveVoting\Exceptions;

use ilException;

/**
 * Class xlvoException
 */
class xlvoException extends ilException {

	/**
	 * @param string $message
	 */
	public function __construct($message) {
		parent::__construct($message);
	}
}
