<?php

namespace LiveVoting\Exceptions;

/**
 * Class xlvoVoterException
 *
 * @package LiveVoting\Exceptions
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoVoterException extends xlvoException {

	const VOTING_OFFLINE = 1;
	const VOTING_NOT_ANONYMOUS = 2;
	const VOTING_PIN_NOT_FOUND = 3;
	const VOTING_UNAVAILABLE = 4;
}
