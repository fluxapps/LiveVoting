<?php

/**
 * Class
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoVotingManagerException extends Exception {

	public function __construct($message, $code = 0, Exception $previous = NULL) {
		parent::__construct($message, $code, $previous);
	}
}