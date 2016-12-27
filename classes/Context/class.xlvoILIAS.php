<?php
namespace LiveVoting\Context;

/**
 * Class xlvoILIAS
 *
 * @package LiveVoting\Context
 */
class xlvoILIAS {

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getSetting($key) {
		global $ilSetting;

		return $ilSetting->get($key);
	}


	/**
	 * wrapper for downward compability
	 */
	public function raiseError($a_msg, $a_err_obj) {
		throw new \ilException($a_msg);
	}
}