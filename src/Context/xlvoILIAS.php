<?php

namespace LiveVoting\Context;

use ilException;
use ilLiveVotingPlugin;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoILIAS
 *
 * @package LiveVoting\Context
 * @author  nschaefli
 */
class xlvoILIAS {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	public function __construct() {

	}


	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	public function getSetting($key) {
		return self::dic()->settings()->get($key);
	}


	/**
	 * wrapper for downward compability
	 *
	 * @throws ilException
	 */
	public function raiseError($a_msg, $a_err_obj) {
		throw new ilException($a_msg);
	}
}
