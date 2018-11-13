<?php

namespace LiveVoting\Session;

use ilLiveVotingPlugin;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoSessionHandler
 *
 * @package LiveVoting\Session
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoSessionHandler {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	/**
	 * @param string $save_path
	 * @param string $sessionid
	 *
	 * @return bool
	 */
	public function open($save_path, $sessionid) {
		return true;
	}


	/**
	 * @return bool
	 */
	public function close() {
		return true;
	}


	/**
	 * @param string $sessionid
	 *
	 * @return string
	 */
	public function read($sessionid) {
		return '';
	}


	/**
	 * @param string $sessionid
	 * @param string $sessiondata
	 *
	 * @return bool|int
	 */
	public function write($sessionid, $sessiondata) {
		return true;
	}


	/**
	 * @param int $sessionid
	 *
	 * @return bool
	 */
	public function destroy($sessionid) {
		return true;
	}


	/**
	 * @param int $maxlifetime
	 *
	 * @return bool
	 */
	public function gc($maxlifetime) {
		return true;
	}
}
