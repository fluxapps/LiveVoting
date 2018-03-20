<?php
/**
 * Created by PhpStorm.
 * User: nschaefli
 * Date: 10/6/16
 * Time: 2:36 PM
 */

namespace LiveVoting\Context\cookie;

use LiveVoting\Context\xlvoContext;

final class CookieManager {

	const PIN_COOKIE = 'xlvo_pin';
	const PIN_COOKIE_FORCE = 'xlvo_force';
	const PUK_COOKIE = 'xlvo_puk';
	const VOTING_COOKIE = 'xlvo_voting';


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
	 * @throws \Exception Throws exception when the given context is invalid.
	 */
	public static function setContext($context) {
		if ($context === xlvoContext::CONTEXT_ILIAS || $context === xlvoContext::CONTEXT_PIN) {
			setcookie(xlvoContext::XLVO_CONTEXT, $context, NULL, '/');
		} else {
			throw new \Exception("invalid context received");
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
	 * @param int $pin
	 */
	public static function setCookiePIN($pin, $forrce = false) {
		setcookie(self::PIN_COOKIE, $pin, NULL, '/');
		if ($forrce) {
			setcookie(self::PIN_COOKIE_FORCE, true, NULL, '/');
		}
	}


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
	 */
	public static function setCookiePUK($puk, $forrce = false) {
		setcookie(self::PUK_COOKIE, $puk, NULL, '/');
	}


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
	 */
	public static function setCookieVoting($voting, $forrce = false) {
		setcookie(self::VOTING_COOKIE, $voting, NULL, '/');
	}


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
}