<?php
namespace LiveVoting\Context;

/**
 * Class xlvoILIAS
 *
 * @package LiveVoting\Context
 */
class xlvoILIAS {

	/**
	 * @var \ilSetting
	 */
	protected $settings;

	public function __construct() {
		global $DIC;
		$this->settings = $DIC["ilSetting"];
	}


	/**
	 * @param $key
	 * @return mixed
	 */
	public function getSetting($key) {
		return $this->settings->get($key);
	}


	/**
	 * wrapper for downward compability
	 */
	public function raiseError($a_msg, $a_err_obj) {
		throw new \ilException($a_msg);
	}
}