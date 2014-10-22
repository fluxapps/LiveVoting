<?php

require_once('Services/Context/classes/class.ilContextBase.php');

class srContextLvo extends ilContextBase {
	public static function supportsRedirects() {
		return false;
	}

	public static function hasUser() {
		return true;
	}

	public static function usesHTTP() {
		return true;
	}

	public static function hasHTML() {
		return true;
	}

	public static function usesTemplate() {
		return true;
	}

	public static function initClient() {
		return true;
	}

	public static function doAuthentication() {
		return false;
	}
}
