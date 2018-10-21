<?php

namespace LiveVoting\Context;

use ilContextTemplate;
use ilLiveVotingPlugin;
use srag\DIC\DICTrait;

/**
 * Class xlvoContextLiveVoting
 *
 * @package LiveVoting\Context
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoContextLiveVoting implements ilContextTemplate {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	/**
	 * @return bool
	 */
	public static function supportsRedirects() {
		return false;
	}


	/**
	 * @return bool
	 */
	public static function hasUser() {
		return true;
	}


	/**
	 * @return bool
	 */
	public static function usesHTTP() {
		return true;
	}


	/**
	 * @return bool
	 */
	public static function hasHTML() {
		return true;
	}


	/**
	 * @return bool
	 */
	public static function usesTemplate() {
		return true;
	}


	/**
	 * @return bool
	 */
	public static function initClient() {
		return true;
	}


	/**
	 * @return bool
	 */
	public static function doAuthentication() {
		return false;
	}


	/**
	 * Check if persistent sessions are supported
	 * false for context cli
	 */
	public static function supportsPersistentSessions() {
		return false;
	}


	/**
	 * Check if push messages are supported, see #0018206
	 *
	 * @return bool
	 */
	public static function supportsPushMessages() {
		return false;
	}
}
