<?php

namespace LiveVoting\Exceptions;

/**
 * Class xlvoVotingManagerException
 *
 * @package LiveVoting\Exceptions
 */
class xlvoVotingManagerException extends xlvoException {

	/**
	 * @param string $message
	 */
	public function __construct($message) {
		parent::__construct($message);
	}
}
