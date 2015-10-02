<?php

$directory = strstr($_SERVER['SCRIPT_FILENAME'], 'Customizing', true);
if ($directory) {
	chdir($directory);
}
require_once('./Services/Init/classes/class.ilInitialisation.php');

/**
 * Class xlvoInitialisation
 *
 * @author      Fabian Schmid <fs@studer-raimann.ch>
 *
 * @description Initializes a ILIAS environment depending on Context (PIN or ILIAS).
 *              This is used in every entry-point for users and AJAX requests
 */
class xlvoInitialisation extends ilInitialisation {

	const USE_OWN_GLOBAL_TPL = true;
	const CONTEXT_PIN = 1;
	const CONTEXT_ILIAS = 2;
	const XLVO_CONTEXT = 'xlvo_context';
	const PIN_COOKIE = 'xlvo_pin';
	const PIN_COOKIE_FORCE = 'xlvo_force';
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
		$this->run();
		global $ilLog;
		/**
		 * @var $ilLog ilLog
		 */
		try{
			throw new Exception();
		}catch (Exception $e) {
			 $ilLog->write('LVO CONTEXT: ' . $this->getContext());//.' '.$e->getTraceAsString());
		}
	}


	protected function run() {
		$this->setContext(self::CONTEXT_ILIAS);
		switch ($this->getContext()) {
			case self::CONTEXT_ILIAS:
				require_once('./include/inc.header.php');
				self::initHTML();
//				self::initILIAS();
				break;
			case self::CONTEXT_PIN:
				require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Context/class.xlvoContext.php");
				require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Context/class.xlvoContextLiveVoting.php");
				xlvoContext::init('xlvoContextLiveVoting');
				self::initILIAS();
				break;
		}
	}


	/**
	 * @param int $context
	 *
	 * @return xlvoInitialisation
	 */
	public static function init($context = NULL) {
		return new self($context);
	}


	public static function destroy() {
//		$pin = NULL;
//		if ($_COOKIE[self::PIN_COOKIE_FORCE]) {
//			$pin = self::getCookiePIN();
//		}
//		session_destroy();
//		foreach ($_COOKIE as $k => $v) {
//			setcookie($k, '', time() - 1000);
//			setcookie($k, '', time() - 1000, '/');
//		}
//
//		unset($_COOKIE);
//		unset($_SESSION);
//		if ($pin) {
//			self::setCookiePIN($pin);
//		}
	}


	/**
	 * set Custom Session handler which does not use db
	 */
	public static function setSessionHandler() {
		require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoSessionHandler.php');
		$session = new xlvoSessionHandler();

		session_set_save_handler(array(
			&$session,
			"open"
		), array(
			&$session,
			"close"
		), array(
			&$session,
			"read"
		), array(
			&$session,
			"write"
		), array(
			&$session,
			"destroy"
		), array(
			&$session,
			"gc"
		));
	}


	public static function initILIAS() {
		if (self::$already_initialized) {
			return;
		}

		self::$already_initialized = true;

		global $tree;
		self::initCore();
		self::initClient();
		self::initUser();
		self::initLanguage();
		$tree->initLangCode();
		self::initHTML();
	}


	protected static function initHTML() {
		parent::initHTML();
		if (self::USE_OWN_GLOBAL_TPL) {
			$tpl = new ilTemplate("tpl.main.html", true, true, 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');
			$tpl->addCss('./templates/default/delos.css');
			$tpl->addBlockFile("CONTENT", "content", "tpl.main_voter.html", 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');

			self::initGlobal("tpl", $tpl);
		}
		global $tpl;
		if (! self::USE_OWN_GLOBAL_TPL) {
			$tpl->getStandardTemplate();
		}
		$tpl->setVariable('BASE', '/'); // FSX TODO set to real root
		if (self::USE_OWN_GLOBAL_TPL) {
			include_once("./Services/jQuery/classes/class.iljQueryUtil.php");
			iljQueryUtil::initjQuery();
			include_once("./Services/UICore/classes/class.ilUIFramework.php");
			ilUIFramework::init();
		}
	}


	protected static function initClient() {
		global $https;

		self::determineClient();
		self::initClientIniFile();
		self::initDatabase();
		if (! is_object($GLOBALS["ilPluginAdmin"])) {
			self::initGlobal("ilPluginAdmin", "ilPluginAdmin", "./Services/Component/classes/class.ilPluginAdmin.php");
		}
		self::setSessionHandler();
		self::initSettings();
		self::initLocale();

		//		if (ilContext::usesHTTP()) {
		//			self::initGlobal("https", "ilHTTPS", "./Services/Http/classes/class.ilHTTPS.php");
		//			$https->enableSecureCookies();
		//			$https->checkPort();
		//		}

		self::initGlobal("ilObjDataCache", "ilObjectDataCache", "./Services/Object/classes/class.ilObjectDataCache.php");
		require_once "./Services/Tree/classes/class.ilTree.php";
		$tree = new ilTree(ROOT_FOLDER_ID);
		self::initGlobal("tree", $tree);
		unset($tree);
		self::initGlobal("ilCtrl", "ilCtrl", "./Services/UICore/classes/class.ilCtrl.php");
		self::setCookieParams();
		self::initLog();
	}


	// PIN COOKIE

	protected function readFromCookie() {
		if (! empty($_COOKIE[self::XLVO_CONTEXT])) {
			self::setContext($_COOKIE[self::XLVO_CONTEXT]);
		} else {
			self::setContext(self::CONTEXT_ILIAS);
		}
	}


	protected function writeToCookie() {
		if ($this->getContext() == self::CONTEXT_ILIAS) {
			//			try{
			//				throw new Exception();
			//			}catch (Exception $e) {
			//				echo $e->getTraceAsString();
			//				exit;
			//			}
		}
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
	protected static function hasCookiePIN() {
		return isset($_COOKIE[self::PIN_COOKIE]);
	}
}
