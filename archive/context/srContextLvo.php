<?php

require_once('Services/Context/classes/class.ilContextBase.php');

/**
 * Class srContextLvo
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 */
class srContextLvo extends ilContextBase {

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
		return false;
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
}
