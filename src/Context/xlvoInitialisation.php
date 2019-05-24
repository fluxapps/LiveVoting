<?php

namespace LiveVoting\Context;

use Exception;
use ILIAS\DI\Container;
use ilInitialisation;
use iljQueryUtil;
use ilLiveVotingPlugin;
use ilTree;
use ilUIFramework;
use LiveVoting\Conf\xlvoConf;
use LiveVoting\Session\xlvoSessionHandler;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoInitialisation
 *
 * @package     LiveVoting\Context
 * @author      Fabian Schmid <fs@studer-raimann.ch>
 *
 * Initializes a ILIAS environment depending on Context (PIN or ILIAS).
 * This is used in every entry-point for users and AJAX requests
 *
 */
class xlvoInitialisation extends ilInitialisation {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	const USE_OWN_GLOBAL_TPL = true;
	const CONTEXT_PIN = 1;
	const CONTEXT_ILIAS = 2;
	/**
	 * @var ilTree
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
			self::setContext(xlvoContext::getContext());
		}
		$this->run();
	}


	/**
	 *
	 */
	protected function run() {
		//		$this->setContext(self::CONTEXT_ILIAS);
		switch (self::getContext()) {
			case self::CONTEXT_ILIAS:
				require_once 'include/inc.header.php';
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


	/**
	 * @param int $context
	 *
	 * @throws Exception
	 */
	public static function saveContext($context) {
		self::setContext($context);
		xlvoContext::setContext($context);
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


	/**
	 *
	 */
	public static function initILIAS2() {
		global $DIC;
		require_once 'include/inc.ilias_version.php';
		self::initDependencyInjection();
		self::initCore();
		self::initClient();
		self::initUser();
		self::initLanguage();
		self::$tree->initLangCode();
		self::initHTML2();
		$GLOBALS["objDefinition"] = $DIC["objDefinition"] = new xlvoObjectDefinition();
	}


	/**
	 *
	 */
	public static function initDependencyInjection() {
		global $DIC;
		$DIC = new Container();
		$DIC["ilLoggerFactory"] = function ($c) {
			return ilLoggerFactory::getInstance();
		};
	}


	/**
	 * @param string $a_name
	 * @param string $a_class
	 * @param null   $a_source_file
	 */
	protected static function initGlobal($a_name, $a_class, $a_source_file = NULL) {
		global $DIC;

		if ($DIC->offsetExists($a_name)) {
			$DIC->offsetUnset($a_name);
		}

		parent::initGlobal($a_name, $a_class, $a_source_file);
	}


	/**
	 *
	 */
	protected static function initHTML2() {
		global $DIC;
		if ($DIC->offsetExists("tpl")) {
			$DIC->offsetUnset("tpl");
		}
		if ($DIC->offsetExists("ilNavigationHistory")) {
			$DIC->offsetUnset("ilNavigationHistory");
		}
		if ($DIC->offsetExists("ilHelp")) {
			$DIC->offsetUnset("ilHelp");
		}
		if ($DIC->offsetExists("styleDefinition")) {
			$DIC->offsetUnset("styleDefinition");
		}
		parent::initHTML();
		if (self::USE_OWN_GLOBAL_TPL) {
			$tpl = self::plugin()->template("default/tpl.main.html");
			$tpl->touchBlock("navbar");
			$tpl->addCss('./templates/default/delos.css');
			$tpl->addBlockFile("CONTENT", "content", "tpl.main_voter.html", self::plugin()->directory());

			if ($DIC->offsetExists("tpl")) {
				$DIC->offsetUnset("tpl");
			}

			self::initGlobal("tpl", $tpl);
		}
		if (!self::USE_OWN_GLOBAL_TPL) {
			$tpl->getStandardTemplate();
		}

		$tpl->setVariable('BASE', xlvoConf::getBaseVoteURL());
		if (self::USE_OWN_GLOBAL_TPL) {
			iljQueryUtil::initjQuery();
			ilUIFramework::init();
		}
	}


	/**
	 *
	 */
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
		//			$https->enableSecureParams();
		//			$https->checkPort();
		//		}

		self::initGlobal("ilObjDataCache", "ilObjectDataCache", "./Services/Object/classes/class.ilObjectDataCache.php");
		self::$tree = new ilTree(ROOT_FOLDER_ID);
		self::initGlobal("tree", self::$tree);
		//unset(self::$tree);
		self::initGlobal("ilCtrl", "ilCtrl", "./Services/UICore/classes/class.ilCtrl.php");
		$GLOBALS['COOKIE_PATH'] = '/';
		self::setParamParams();
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
