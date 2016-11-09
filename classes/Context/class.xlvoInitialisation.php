<?php
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
	protected static $context = self::CONTEXT_PIN;


	/**
	 * xlvoInitialisation constructor.
	 *
	 * @param int $context
	 */
	protected function __construct($context = null) {
		if ($context) {
			self::saveContext($context);
		} else {
			self::readFromCookie();
		}
		$this->run();
	}


	protected function run() {
		//		$this->setContext(self::CONTEXT_ILIAS);
		switch (self::getContext()) {
			case self::CONTEXT_ILIAS:
				require_once('./include/inc.header.php');
				self::initHTML2();
				//				self::initILIAS();
				break;
			case self::CONTEXT_PIN:
				require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Context/class.xlvoContext.php");
				require_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Context/class.xlvoContextLiveVoting.php");
				xlvoContext::init('xlvoContextLiveVoting');
				self::initILIAS2();
				break;
		}
	}


	/**
	 * @param int $context
	 *
	 * @return xlvoInitialisation
	 */
	public static function init($context = null) {
		return new self($context);
	}


	public static function saveContext($context) {
		self::setContext($context);
		self::writeToCookie();
	}


	/**
	 * set Custom Session handler which does not use db
	 */
	public static function setSessionHandler() {
		require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoSessionHandler.php');
		$session = new xlvoSessionHandler();

		session_set_save_handler(array(
			&$session,
			"open",
		), array(
			&$session,
			"close",
		), array(
			&$session,
			"read",
		), array(
			&$session,
			"write",
		), array(
			&$session,
			"destroy",
		), array(
			&$session,
			"gc",
		));
	}


	public static function initILIAS2() {
		global $tree;
		require_once("./include/inc.ilias_version.php");
		if(version_compare(ILIAS_VERSION_NUMERIC, '5.2.00', '>=')) {
			self::initDependencyInjection();
		}
		self::initCore();
		self::initClient();
		self::initUser();
		self::initLanguage();
		$tree->initLangCode();
		self::initHTML2();
		require_once('class.xlvoObjectDefinition.php');
		global $objDefinition;
		$objDefinition = new xlvoObjectDefinition();
	}

	public static function initDependencyInjection() {
		$GLOBALS["DIC"] = new \ILIAS\DI\Container();
		$GLOBALS["DIC"]["ilLoggerFactory"] = function($c) {
			return ilLoggerFactory::getInstance();
		};
	}


	protected static function initHTML2() {
		parent::initHTML();
		if (self::USE_OWN_GLOBAL_TPL) {
			$tpl = new ilTemplate("tpl.main.html", true, true, 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');
			$tpl->addCss('./templates/default/delos.css');
			$tpl->addBlockFile("CONTENT", "content", "tpl.main_voter.html", 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');

			self::initGlobal("tpl", $tpl);
		}
		global $tpl;
		if (!self::USE_OWN_GLOBAL_TPL) {
			$tpl->getStandardTemplate();
		}
		require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Conf/class.xlvoConf.php');
		$tpl->setVariable('BASE', xlvoConf::getBaseURL());
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
		if (!is_object($GLOBALS["ilPluginAdmin"])) {
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
		$GLOBALS['COOKIE_PATH'] = '/';
		self::setCookieParams();
		self::initLog();
	}


	// PIN COOKIE

	protected static function readFromCookie() {
		if (!empty($_COOKIE[self::XLVO_CONTEXT])) {
			self::setContext($_COOKIE[self::XLVO_CONTEXT]);
		} else {
			self::setContext(self::CONTEXT_ILIAS);
		}
	}


	protected static function writeToCookie() {
		setcookie(self::XLVO_CONTEXT, self::getContext(), null, '/');
	}


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
		setcookie(self::PIN_COOKIE, $pin, null, '/');
		if ($forrce) {
			setcookie(self::PIN_COOKIE_FORCE, true, null, '/');
		}
	}


	public static function resetCookiePIN() {
		if ($_COOKIE[self::PIN_COOKIE_FORCE]) {
			unset($_COOKIE[self::PIN_COOKIE_FORCE]);
			setcookie(self::PIN_COOKIE_FORCE, null, - 1, '/');
		} else {
			unset($_COOKIE[self::PIN_COOKIE]);
			setcookie(self::PIN_COOKIE, null, - 1, '/');
		}
	}


	/**
	 * @return bool
	 */
	protected static function hasCookiePIN() {
		return isset($_COOKIE[self::PIN_COOKIE]);
	}
}
