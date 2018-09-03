<?php

namespace srag\DIC\DIC;

use Collator;
use ilAccess;
use ilAppEventHandler;
use ilAuthSession;
use ilBenchmark;
use ilBrowser;
use ilCtrl;
use ilCtrlStructureReader;
use ilDBInterface;
use ilErrorHandling;
use ilHelpGUI;
use ILIAS;
use ILIAS\DI\BackgroundTaskServices;
use ILIAS\DI\HTTPServices;
use ILIAS\DI\LoggingServices;
use ILIAS\DI\UIServices;
use ILIAS\Filesystem\Filesystems;
use ILIAS\FileUpload\FileUpload;
use ilIniFile;
use ilLanguage;
use ilLocatorGUI;
use ilLog;
use ilLoggerFactory;
use ilMailMimeSenderFactory;
use ilMainMenuGUI;
use ilNavigationHistory;
use ilObjectDataCache;
use ilObjectDefinition;
use ilObjUser;
use ilPluginAdmin;
use ilRbacAdmin;
use ilRbacReview;
use ilRbacSystem;
use ilSetting;
use ilStyleDefinition;
use ilTabsGUI;
use ilTemplate;
use ilToolbarGUI;
use ilTree;
use Session;
use srag\DIC\Exception\DICException;

/**
 * Interface DICInterface
 *
 * @package srag\DIC\DIC
 */
interface DICInterface {

	/**
	 * @return ilAccess
	 */
	public function access();


	/**
	 * @return ilAppEventHandler
	 */
	public function appEventHandler();


	/**
	 * @return ilAuthSession
	 */
	public function authSession();


	/**
	 * @return BackgroundTaskServices
	 *
	 * @throws DICException BackgroundTaskServices not exists in ILIAS 5.2 or below!
	 */
	public function backgroundTasks();


	/**
	 * @return ilBenchmark
	 */
	public function benchmark();


	/**
	 * @return ilBrowser
	 */
	public function browser();


	/**
	 * @return ilIniFile
	 */
	public function clientIni();


	/**
	 * @return Collator
	 */
	public function collator();


	/**
	 * @return ilCtrl
	 */
	public function ctrl();


	/**
	 * @return ilCtrlStructureReader
	 */
	public function ctrlStructureReader();


	/**
	 * @return ilDBInterface
	 */
	public function database();


	/**
	 * @return ilErrorHandling
	 */
	public function error();


	/**
	 * @return Filesystems
	 *
	 * @throws DICException Filesystems not exists in ILIAS 5.2 or below!
	 */
	public function filesystem();


	/**
	 * @return ilHelpGUI
	 */
	public function help();


	/**
	 * @return ilNavigationHistory
	 */
	public function history();


	/**
	 * @return HTTPServices
	 *
	 * @throws DICException HTTPServices not exists in ILIAS 5.2 or below!
	 */
	public function http();


	/**
	 * @return ILIAS
	 */
	public function ilias();


	/**
	 * @return ilIniFile
	 */
	public function iliasIni();


	/**
	 * @return ilLanguage
	 */
	public function language();


	/**
	 * @return ilLocatorGUI
	 */
	public function locator();


	/**
	 * @return ilLog
	 */
	public function log();


	/**
	 * @return LoggingServices
	 *
	 * @throws DICException LoggingServices not exists in ILIAS 5.2 or below!
	 */
	public function logger();


	/**
	 * @return ilLoggerFactory
	 */
	public function loggerFactory();


	/**
	 * @return ilMainMenuGUI
	 */
	public function mainMenu();


	/**
	 * @return ilMailMimeSenderFactory
	 *
	 * @throws DICException ilMailMimeSenderFactory not exists in ILIAS 5.2 or below!
	 */
	public function mailMimeSenderFactory();


	/**
	 * @return ilObjectDataCache
	 */
	public function objDataCache();


	/**
	 * @return ilObjectDefinition
	 */
	public function objDefinition();


	/**
	 * @return ilPluginAdmin
	 */
	public function pluginAdmin();


	/**
	 * @return ilRbacAdmin
	 */
	public function rbacadmin();


	/**
	 * @return ilRbacReview
	 */
	public function rbacreview();


	/**
	 * @return ilRbacSystem
	 */
	public function rbacsystem();


	/**
	 * @return Session
	 */
	public function session();


	/**
	 * @return ilSetting
	 */
	public function settings();


	/**
	 * @return ilStyleDefinition
	 */
	public function systemStyle();


	/**
	 * @return ilTabsGUI
	 */
	public function tabs();


	/**
	 * @return ilTemplate Main-Template
	 */
	public function template();


	/**
	 * @return ilToolbarGUI
	 */
	public function toolbar();


	/**
	 * @return ilTree
	 */
	public function tree();


	/**
	 * @return UIServices
	 *
	 * @throws DICException UIServices not exists in ILIAS 5.1 or below!
	 */
	public function ui();


	/**
	 * @return FileUpload
	 *
	 * @throws DICException FileUpload not exists in ILIAS 5.2 or below!
	 */
	public function upload();


	/**
	 * @return ilObjUser
	 */
	public function user();
}
