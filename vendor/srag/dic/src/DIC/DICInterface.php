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
	public function access()/*: ilAccess*/
	;


	/**
	 * @return ilAppEventHandler
	 */
	public function appEventHandler()/*: ilAppEventHandler*/
	;


	/**
	 * @return ilAuthSession
	 */
	public function authSession()/*: ilAuthSession*/
	;


	/**
	 * @return BackgroundTaskServices
	 *
	 * @throws DICException BackgroundTaskServices not exists in ILIAS 5.2 or below!
	 */
	public function backgroundTasks()/*: BackgroundTaskServices*/
	;


	/**
	 * @return ilBenchmark
	 */
	public function benchmark()/*: ilBenchmark*/
	;


	/**
	 * @return ilBrowser
	 */
	public function browser()/*: ilBrowser*/
	;


	/**
	 * @return ilIniFile
	 */
	public function clientIni()/*: ilIniFile*/
	;


	/**
	 * @return Collator
	 */
	public function collator()/*: Collator*/
	;


	/**
	 * @return ilCtrl
	 */
	public function ctrl()/*: ilCtrl*/
	;


	/**
	 * @return ilCtrlStructureReader
	 */
	public function ctrlStructureReader()/*: ilCtrlStructureReader*/
	;


	/**
	 * @return ilDBInterface
	 */
	public function database()/*: ilDBInterface*/
	;


	/**
	 * @return ilErrorHandling
	 */
	public function error()/*: ilErrorHandling*/
	;


	/**
	 * @return Filesystems
	 *
	 * @throws DICException Filesystems not exists in ILIAS 5.2 or below!
	 */
	public function filesystem()/*: Filesystems*/
	;


	/**
	 * @return ilHelpGUI
	 */
	public function help()/*: ilHelpGUI*/
	;


	/**
	 * @return ilNavigationHistory
	 */
	public function history()/*: ilNavigationHistory*/
	;


	/**
	 * @return HTTPServices
	 *
	 * @throws DICException HTTPServices not exists in ILIAS 5.2 or below!
	 */
	public function http()/*: HTTPServices*/
	;


	/**
	 * @return ILIAS
	 */
	public function ilias()/*: ILIAS*/
	;


	/**
	 * @return ilIniFile
	 */
	public function iliasIni()/*: ilIniFile*/
	;


	/**
	 * @return ilLanguage
	 */
	public function language()/*: ilLanguage*/
	;


	/**
	 * @return ilLocatorGUI
	 */
	public function locator()/*: ilLocatorGUI*/
	;


	/**
	 * @return ilLog
	 */
	public function log()/*: ilLog*/
	;


	/**
	 * @return LoggingServices
	 *
	 * @throws DICException LoggingServices not exists in ILIAS 5.2 or below!
	 */
	public function logger()/*: LoggingServices*/
	;


	/**
	 * @return ilLoggerFactory
	 */
	public function loggerFactory()/*: ilLoggerFactory*/
	;


	/**
	 * @return ilMailMimeSenderFactory
	 *
	 * @throws DICException ilMailMimeSenderFactory not exists in ILIAS 5.2 or below!
	 */
	public function mailMimeSenderFactory()/*: ilMailMimeSenderFactory*/
	;


	/**
	 * @return ilMainMenuGUI
	 */
	public function mainMenu()/*: ilMainMenuGUI*/
	;


	/**
	 * @return ilObjectDataCache
	 */
	public function objDataCache()/*: ilObjectDataCache*/
	;


	/**
	 * @return ilObjectDefinition
	 */
	public function objDefinition()/*: ilObjectDefinition*/
	;


	/**
	 * @return ilPluginAdmin
	 */
	public function pluginAdmin()/*: ilPluginAdmin*/
	;


	/**
	 * @return ilRbacAdmin
	 */
	public function rbacadmin()/*: ilRbacAdmin*/
	;


	/**
	 * @return ilRbacReview
	 */
	public function rbacreview()/*: ilRbacReview*/
	;


	/**
	 * @return ilRbacSystem
	 */
	public function rbacsystem()/*: ilRbacSystem*/
	;


	/**
	 * @return Session
	 */
	public function session()/*: Session*/
	;


	/**
	 * @return ilSetting
	 */
	public function settings()/*: ilSetting*/
	;


	/**
	 * @return ilStyleDefinition
	 */
	public function systemStyle()/*: ilStyleDefinition*/
	;


	/**
	 * @return ilTabsGUI
	 */
	public function tabs()/*: ilTabsGUI*/
	;


	/**
	 * @return ilTemplate Main-Template
	 */
	public function template()/*: ilTemplate*/
	;


	/**
	 * @return ilToolbarGUI
	 */
	public function toolbar()/*: ilToolbarGUI*/
	;


	/**
	 * @return ilTree
	 */
	public function tree()/*: ilTree*/
	;


	/**
	 * @return UIServices
	 *
	 * @throws DICException UIServices not exists in ILIAS 5.1 or below!
	 */
	public function ui()/*: UIServices*/
	;


	/**
	 * @return FileUpload
	 *
	 * @throws DICException FileUpload not exists in ILIAS 5.2 or below!
	 */
	public function upload()/*: FileUpload*/
	;


	/**
	 * @return ilObjUser
	 */
	public function user()/*: ilObjUser*/
	;
}
