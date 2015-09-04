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

	const USE_OWN_GLOBAL_TPL = false;
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
		$this->run();
	}


	protected function run() {
		switch ($this->getContext()) {
			case self::CONTEXT_ILIAS:
				require_once('./include/inc.header.php');

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
	 * @param null $context
	 *
	 * @return xlvoInitialisation
	 */
	public static function init($context = NULL) {
		return new self($context);
	}


	/**
	 * set session handler to db
	 *
	 * Used in Soap/CAS
	 */
	public static function setSessionHandler() {
		require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoSessionHandler.php');
		session_set_save_handler(new xlvoSessionHandler());
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
