<?php

namespace LiveVoting\Exceptions;

use ilException;
use ilLiveVotingPlugin;
use srag\DIC\DICTrait;

/**
 * Class xlvoException
 *
 * @package LiveVoting\Exceptions
 */
class xlvoException extends ilException {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	/**
	 * @param string $message
	 * @param int    $a_code
	 */
	public function __construct($message, $a_code = 0) {
		parent::__construct($message, $a_code);
	}
}
