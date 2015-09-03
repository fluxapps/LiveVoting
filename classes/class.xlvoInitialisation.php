<?php

/**
 * Class xlvoInitialisation
 *
 * @author      Fabian Schmid <fs@studer-raimann.ch>
 *
 * @description Initializes a ILIAS environment depending on Context (PIN or ILIAS).
 *              This is used in every entry-point for users and AJAX requests
 */
class xlvoInitialisation {

	const CONTEXT_PIN = 1;
	const CONTEXT_ILIAS = 2;
	const XLVO_CONTEXT = 'xlvo_context';
	const PIN_COOKIE = 'xlvo_pin';
	/**
	 * @var int
	 */
	protected $context = self::CONTEXT_PIN;


	/**
	 * xlvoInitialisation constructor.
	 *
	 * @param int $context
	 */
	protected function __construct($context = NULL) {
		if ($context) {
			$this->context = $context;
			$this->writeToCookie();
		} else {
			$this->readFromCookie();
		}
		$this->initILIAS();
	}


	/**
	 * @param null $context
	 *
	 * @return xlvoInitialisation
	 */
	public static function init($context = NULL) {
		return new self($context);
	}


	protected function readFromCookie() {
		if (! empty($_COOKIE[self::XLVO_CONTEXT])) {
			self::setContext($_COOKIE[self::XLVO_CONTEXT]);
		} else {
			self::setContext(self::CONTEXT_ILIAS);
		}
	}


	protected function writeToCookie() {
		setcookie(self::XLVO_CONTEXT, $this->getContext(), NULL, '/');
	}


	/**
	 * @return int
	 */
	public function getContext() {
		return $this->context;
	}


	/**
	 * @param int $context
	 */
	public function setContext($context) {
		$this->context = $context;
	}


	protected function initILIAS() {
		chdir(strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true));

		switch ($this->getContext()) {
			case self::CONTEXT_ILIAS:
				require_once('./include/inc.header.php');

				break;
			case self::CONTEXT_PIN:
				require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/context/srContext.php");
				require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/context/srContextLvo.php");
				require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/context/srInitialisation.php");

				srContext::init('srContextLvo');
				srInitialisation::initILIAS();
				break;
		}
	}


	/**
	 * @return int
	 */
	public static function getCookiePIN() {
		if (! self::hasCookiePIN()) {
			return false;
		}

		return $_COOKIE[self::PIN_COOKIE];
	}


	/**
	 * @param int $pin
	 */
	public static function setCookiePIN($pin) {
		setcookie(self::PIN_COOKIE, $pin, NULL, '/');
	}


	public static function resetCookiePIN() {
		unset($_COOKIE[self::PIN_COOKIE]);
		setcookie(self::PIN_COOKIE, NULL, - 1, '/');
	}


	/**
	 * @return bool
	 */
	protected static function hasCookiePIN() {
		return $_COOKIE[self::PIN_COOKIE] > 0;
	}
}
