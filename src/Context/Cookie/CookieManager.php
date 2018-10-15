<?php

namespace LiveVoting\Context\Cookie;

use Exception;
use ilLiveVotingPlugin;
use LiveVoting\Context\xlvoContext;
use srag\DIC\DICTrait;

/**
 * Class CookieManager
 *
 * @package LiveVoting\Context\Cookie
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 *
 */
final class CookieManager {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	const PIN_COOKIE = 'xlvo_pin';
	const PIN_COOKIE_FORCE = 'xlvo_force';
	const PUK_COOKIE = 'xlvo_puk';
	const VOTING_COOKIE = 'xlvo_voting';
	const PPT_COOKIE = 'xlvo_ppt';


	/**
	 * @return int
	 */
	public static function getContext() {
		if (!empty($_COOKIE[xlvoContext::XLVO_CONTEXT])) {
			return $_COOKIE[xlvoContext::XLVO_CONTEXT];
		}

		return xlvoContext::CONTEXT_ILIAS;
	}


	/**
	 * Sets the xlvo context cookie.
	 * This cookie is used to determine the needed bootstrap process.
	 * The context constants can be found in the xlvoContext class.
	 *
	 * @param int $context CONTEXT_ILIAS or CONTEXT_PIN are valid options.
	 *
	 * @throws Exception Throws exception when the given context is invalid.
	 */
	public static function setContext($context) {
		if ($context === xlvoContext::CONTEXT_ILIAS || $context === xlvoContext::CONTEXT_PIN) {
			$result = setcookie(xlvoContext::XLVO_CONTEXT, $context, NULL, '/');
		} else {
			throw new Exception("invalid context received");
		}
		if (!$result) {
			throw new Exception("error setting cookie");
		}
	}


	/**
	 * @return int
	 */
	public static function getCookiePIN() {
		if (!self::hasCookiePIN()) {
			return false;
		}

		return $_COOKIE[self::PIN_COOKIE];
	}


	/**
	 * @param int  $pin
	 * @param bool $force
	 *
	 * @throws Exception
	 */
	public static function setCookiePIN($pin, $force = false) {
		$result = setcookie(self::PIN_COOKIE, $pin, NULL, '/');
		if ($force) {
			$result = setcookie(self::PIN_COOKIE_FORCE, true, NULL, '/');
		}
		if (!$result) {
			throw new Exception("error setting cookie");
		}
	}


	/**
	 *
	 */
	public static function resetCookiePIN() {
		if ($_COOKIE[self::PIN_COOKIE_FORCE]) {
			unset($_COOKIE[self::PIN_COOKIE_FORCE]);
			setcookie(self::PIN_COOKIE_FORCE, NULL, - 1, '/');
		} else {
			unset($_COOKIE[self::PIN_COOKIE]);
			setcookie(self::PIN_COOKIE, NULL, - 1, '/');
		}
	}


	/**
	 * @return bool
	 */
	private static function hasCookiePIN() {
		return isset($_COOKIE[self::PIN_COOKIE]);
	}


	/**
	 * @return string
	 */
	public static function getCookiePUK() {
		if (!self::hasCookiePUK()) {
			return false;
		}

		return $_COOKIE[self::PUK_COOKIE];
	}


	/**
	 * @param string $puk
	 * @param bool   $force
	 *
	 * @throws Exception
	 */
	public static function setCookiePUK($puk, $force = false) {
		$result = setcookie(self::PUK_COOKIE, $puk, NULL, '/');
		if (!$result) {
			throw new Exception("error setting cookie");
		}
	}


	/**
	 *
	 */
	public static function resetCookiePUK() {
		if (isset($_COOKIE[self::PUK_COOKIE])) {
			unset($_COOKIE[self::PUK_COOKIE]);
			setcookie(self::PUK_COOKIE, NULL, - 1, '/');
		}
	}


	/**
	 * @return bool
	 */
	public static function hasCookiePUK() {
		return isset($_COOKIE[self::PUK_COOKIE]);
	}


	/**
	 * @return string
	 */
	public static function getCookieVoting() {
		if (!self::hasCookieVoting()) {
			return false;
		}

		return $_COOKIE[self::VOTING_COOKIE];
	}


	/**
	 * @param string $voting
	 * @param bool   $force
	 *
	 * @throws Exception
	 */
	public static function setCookieVoting($voting, $force = false) {
		$result = setcookie(self::VOTING_COOKIE, $voting, NULL, '/');
		if (!$result) {
			throw new Exception("error setting cookie");
		}
	}


	/**
	 *
	 */
	public static function resetCookieVoting() {
		if (isset($_COOKIE[self::VOTING_COOKIE])) {
			unset($_COOKIE[self::VOTING_COOKIE]);
			setcookie(self::VOTING_COOKIE, NULL, - 1, '/');
		}
	}


	/**
	 * @return bool
	 */
	public static function hasCookieVoting() {
		return isset($_COOKIE[self::VOTING_COOKIE]);
	}


	/**
	 * @return bool
	 */
	public static function getCookiePpt() {
		if (!self::hasCookiePpt()) {
			return false;
		}

		return boolval($_COOKIE[self::PPT_COOKIE]);
	}


	/**
	 * @param bool|int|string $ppt
	 * @param bool            $force
	 *
	 * @throws Exception
	 */
	public static function setCookiePpt($ppt, $force = false) {
		// Fix short url
		if ($ppt === "ppt") {
			$ppt = true;
		}
		$result = setcookie(self::PPT_COOKIE, boolval($ppt), NULL, '/');
		if (!$result) {
			throw new Exception("error setting cookie");
		}
	}


	/**
	 *
	 */
	public static function resetCookiePpt() {
		if (isset($_COOKIE[self::PPT_COOKIE])) {
			unset($_COOKIE[self::PPT_COOKIE]);
			setcookie(self::PPT_COOKIE, NULL, - 1, '/');
		}
	}


	/**
	 * @return bool
	 */
	public static function hasCookiePpt() {
		return isset($_COOKIE[self::PPT_COOKIE]);
	}
}
