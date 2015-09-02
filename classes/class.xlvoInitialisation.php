<?php

/**
 * Class xlvoInitialisation
 */
class xlvoInitialisation {

	const CONTEXT_PIN = 1;
	const CONTEXT_ILIAS = 2;
	/**
	 * @var int
	 */
	protected static $context = self::CONTEXT_PIN;


	public static function initILIAS() {
		//		session_start();
		chdir(strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true));
		//		self::readFromCookie();
		//		echo 'read ' . var_dump($_SESSION['xlvo_context']);
		//		echo 'get ' . self::getContext();
		//		echo var_dump($_SESSION['xlvo_context']);
		//		exit;
		self::readFromSession();

		switch (self::getContext()) {
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


	public static function readFromSession() {
		//		echo 'read ' . empty($_SESSION['xlvo_context']);
		//		echo 'get ' . self::getContext();
		//		exit;
		if (! empty($_SESSION['xlvo_context'])) {
			self::setContext($_SESSION['xlvo_context']);
		} else {
			self::setContext(self::CONTEXT_PIN);
		}
	}


	public static function writeToSession($context) {
		$_SESSION['xlvo_context'] = $context;
	}


	//	public static function readFromCookie() {
	//		if (! empty($_COOKIE['lxvo_context'])) {
	//			self::setContext($_COOKIE['xlvo_context']);
	//		} else {
	//			self::setContext(self::CONTEXT_PIN);
	//		}
	//	}
	//
	//
	//	public static function writeToCookie($context) {
	//		//		if (empty($_COOKIE['lxvo_context']) || (self::getContext() != self::CONTEXT_PIN)) {
	//		//			unset($_COOKIE['lxvo_context']);
	//		//			setcookie('xlvo_context', $context, time() + 3600, "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting", "", 0);
	//		setcookie('xlvo_context', $context, time() + 3600, "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting");
	//		//		}
	//	}

	/**
	 * @return int
	 */
	public static function getContext() {
		return self::$context;
	}


	/**
	 * @param int $context
	 */
	public static function setContext($context) {
		self::$context = $context;
	}
}
