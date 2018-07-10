<?php

namespace LiveVoting\Exceptions;

/**
 * Class xlvoSubFormGUIHandleFieldException
 */
class xlvoSubFormGUIHandleFieldException extends xlvoException {

	/**
	 * @param string $message
	 */
	public function __construct($message) {
		parent::__construct($message);
	}
}
