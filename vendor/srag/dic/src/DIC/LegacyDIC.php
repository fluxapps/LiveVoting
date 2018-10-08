<?php

namespace srag\DIC\DIC;

use ilLoggerFactory;
use srag\DIC\Exception\DICException;

/**
 * Class LegacyDIC
 *
 * @package srag\DIC\DIC
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class LegacyDIC extends AbstractDIC {

	/**
	 * @var array
	 */
	private $globals;


	/**
	 * LegacyDIC constructor
	 *
	 * @param array $globals
	 *
	 * @access namespace
	 */
	public function __construct(array &$globals) {
		parent::__construct();

		$this->globals = &$globals;
	}


	/**
	 * @inheritdoc
	 */
	public function access()/*: ilAccess*/ {
		return $this->globals["ilAccess"];
	}


	/**
	 * @inheritdoc
	 */
	public function appEventHandler()/*: ilAppEventHandler*/ {
		return $this->globals["ilAppEventHandler"];
	}


	/**
	 * @inheritdoc
	 */
	public function authSession()/*: ilAuthSession*/ {
		return $this->globals["ilAuthSession"];
	}


	/**
	 * @inheritdoc
	 */
	public function backgroundTasks()/*: BackgroundTaskServices*/ {
		throw new DICException("BackgroundTaskServices not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function benchmark()/*: ilBenchmark*/ {
		return $this->globals["ilBench"];
	}


	/**
	 * @inheritdoc
	 */
	public function browser()/*: ilBrowser*/ {
		return $this->globals["ilBrowser"];
	}


	/**
	 * @inheritdoc
	 */
	public function clientIni()/*: ilIniFile*/ {
		return $this->globals["ilClientIniFile"];
	}


	/**
	 * @inheritdoc
	 */
	public function collator()/*: Collator*/ {
		return $this->globals["ilCollator"];
	}


	/**
	 * @inheritdoc
	 */
	public function ctrl()/*: ilCtrl*/ {
		return $this->globals["ilCtrl"];
	}


	/**
	 * @inheritdoc
	 */
	public function ctrlStructureReader()/*: ilCtrlStructureReader*/ {
		return $this->globals["ilCtrlStructureReader"];
	}


	/**
	 * @inheritdoc
	 */
	public function database()/*: ilDBInterface*/ {
		return $this->globals["ilDB"];
	}


	/**
	 * @inheritdoc
	 */
	public function error()/*: ilErrorHandling*/ {
		return $this->globals["ilErr"];
	}


	/**
	 * @inheritdoc
	 */
	public function filesystem()/*: Filesystems*/ {
		throw new DICException("Filesystems not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function help()/*: ilHelpGUI*/ {
		return $this->globals["ilHelp"];
	}


	/**
	 * @inheritdoc
	 */
	public function history()/*: ilNavigationHistory*/ {
		return $this->globals["ilNavigationHistory"];
	}


	/**
	 * @inheritdoc
	 */
	public function http()/*: HTTPServices*/ {
		throw new DICException("HTTPServices not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function ilias()/*: ILIAS*/ {
		return $this->globals["ilias"];
	}


	/**
	 * @inheritdoc
	 */
	public function iliasIni()/*: ilIniFile*/ {
		return $this->globals["ilIliasIniFile"];
	}


	/**
	 * @inheritdoc
	 */
	public function language()/*: ilLanguage*/ {
		return $this->globals["lng"];
	}


	/**
	 * @inheritdoc
	 */
	public function locator()/*: ilLocatorGUI*/ {
		return $this->globals["ilLocator"];
	}


	/**
	 * @inheritdoc
	 */
	public function log()/*: ilLog*/ {
		return $this->globals["ilLog"];
	}


	/**
	 * @inheritdoc
	 */
	public function logger()/*: LoggingServices*/ {
		throw new DICException("LoggingServices not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function loggerFactory()/*: ilLoggerFactory*/ {
		return ilLoggerFactory::getInstance();
	}


	/**
	 * @inheritdoc
	 */
	public function mailMimeSenderFactory()/*: ilMailMimeSenderFactory*/ {
		throw new DICException("ilMailMimeSenderFactory not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function mailMimeTransportFactory()/*: ilMailMimeTransportFactory*/ {
		throw new DICException("ilMailMimeTransportFactory not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function mainMenu()/*: ilMainMenuGUI*/ {
		return $this->globals["ilMainMenu"];
	}


	/**
	 * @inheritdoc
	 */
	public function mainTemplate()/*: ilTemplate*/ {
		return $this->globals["tpl"];
	}


	/**
	 * @inheritdoc
	 */
	public function objDataCache()/*: ilObjectDataCache*/ {
		return $this->globals["ilObjDataCache"];
	}


	/**
	 * @inheritdoc
	 */
	public function objDefinition()/*: ilObjectDefinition*/ {
		return $this->globals["objDefinition"];
	}


	/**
	 * @inheritdoc
	 */
	public function pluginAdmin()/*: ilPluginAdmin*/ {
		return $this->globals["ilPluginAdmin"];
	}


	/**
	 * @inheritdoc
	 */
	public function rbacadmin()/*: ilRbacAdmin*/ {
		return $this->globals["rbacadmin"];
	}


	/**
	 * @inheritdoc
	 */
	public function rbacreview()/*: ilRbacReview*/ {
		return $this->globals["rbacreview"];
	}


	/**
	 * @inheritdoc
	 */
	public function rbacsystem()/*: ilRbacSystem*/ {
		return $this->globals["rbacsystem"];
	}


	/**
	 * @inheritdoc
	 */
	public function session()/*: Session*/ {
		return $this->globals["sess"];
	}


	/**
	 * @inheritdoc
	 */
	public function settings()/*: ilSetting*/ {
		return $this->globals["ilSetting"];
	}


	/**
	 * @inheritdoc
	 */
	public function systemStyle()/*: ilStyleDefinition*/ {
		return $this->globals["styleDefinition"];
	}


	/**
	 * @inheritdoc
	 */
	public function tabs()/*: ilTabsGUI*/ {
		return $this->globals["ilTabs"];
	}


	/**
	 * @inheritdoc
	 */
	public function toolbar()/*: ilToolbarGUI*/ {
		return $this->globals["ilToolbar"];
	}


	/**
	 * @inheritdoc
	 */
	public function tree()/*: ilTree*/ {
		return $this->globals["tree"];
	}


	/**
	 * @inheritdoc
	 */
	public function ui()/*: UIServices*/ {
		throw new DICException("UIServices not exists in ILIAS 5.1 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function upload()/*: FileUpload*/ {
		throw new DICException("FileUpload not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function user()/*: ilObjUser*/ {
		return $this->globals["ilUser"];
	}


	/**
	 * @return array
	 */
	public function &globals()/*: array*/ {
		return $this->globals;
	}
}
