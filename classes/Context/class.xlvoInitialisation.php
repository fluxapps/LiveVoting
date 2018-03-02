<?php

namespace LiveVoting\Context;

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\cookie\CookieManager;
use LiveVoting\xlvoSessionHandler;

require_once('./Services/Init/classes/class.ilInitialisation.php');

/**
 * Class xlvoInitialisation
 *
 * @author      Fabian Schmid <fs@studer-raimann.ch>
 *
 * @description Initializes a ILIAS environment depending on Context (PIN or ILIAS).
 *              This is used in every entry-point for users and AJAX requests
 */
class xlvoInitialisation extends \ilInitialisation {

	const USE_OWN_GLOBAL_TPL = true;
	const CONTEXT_PIN = 1;
	const CONTEXT_ILIAS = 2;
	/**
	 * @var \ilTree
	 */
	protected static $tree;
	/**
	 * @var int
	 */
	protected static $context = self::CONTEXT_PIN;


	/**
	 * xlvoInitialisation constructor.
	 *
	 * @param int $context
	 */
	protected function __construct($context = NULL) {
		if ($context) {
			self::saveContext($context);
		} else {
			self::setContext(CookieManager::getContext());
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
				xlvoContext::init(xlvoContextLiveVoting::class);
				self::initILIAS2();
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


	public static function saveContext($context) {
		self::setContext($context);
		CookieManager::setContext($context);
	}


	/**
	 * set Custom Session handler which does not use db
	 */
	public static function setSessionHandler() {
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
		global $DIC;
		require_once("./include/inc.ilias_version.php");
		self::initDependencyInjection();
		self::initCore();
		self::initClient();
		self::initUser();
		self::initLanguage();
		self::$tree->initLangCode();
		self::initHTML2();
		$GLOBALS["objDefinition"] = $DIC["objDefinition"] = new xlvoObjectDefinition();
	}


	public static function initDependencyInjection() {
		global $DIC;
		$DIC = new \ILIAS\DI\Container();
		$DIC["ilLoggerFactory"] = function ($c) {
			return ilLoggerFactory::getInstance();
		};
	}


	protected static function initHTML2() {
		parent::initHTML();
		if (self::USE_OWN_GLOBAL_TPL) {
			$tpl = new \ilTemplate("tpl.main.html", true, true, 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');
			$tpl->touchBlock("navbar");
			$tpl->addCss('./templates/default/delos.css');
			$tpl->addBlockFile("CONTENT", "content", "tpl.main_voter.html", 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');

			self::initGlobal("tpl", $tpl);
		}
		if (!self::USE_OWN_GLOBAL_TPL) {
			$tpl->getStandardTemplate();
		}
		require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Conf/class.xlvoConf.php');
		$tpl->setVariable('BASE', xlvoConf::getBaseURL());
		if (self::USE_OWN_GLOBAL_TPL) {
			include_once("./Services/jQuery/classes/class.iljQueryUtil.php");
			\iljQueryUtil::initjQuery();
			include_once("./Services/UICore/classes/class.ilUIFramework.php");
			\ilUIFramework::init();
		}
	}


	protected static function initClient() {
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
		self::$tree = new \ilTree(ROOT_FOLDER_ID);
		self::initGlobal("tree", self::$tree);
		//unset(self::$tree);
		self::initGlobal("ilCtrl", "ilCtrl", "./Services/UICore/classes/class.ilCtrl.php");
		$GLOBALS['COOKIE_PATH'] = '/';
		self::setCookieParams();
		self::initLog();
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
}
