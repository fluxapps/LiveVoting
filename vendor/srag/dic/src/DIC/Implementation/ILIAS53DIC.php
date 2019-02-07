<?php

namespace srag\DIC\LiveVoting\DIC\Implementation;

use ILIAS\DI\Container;
use srag\DIC\LiveVoting\DIC\AbstractDIC;
use srag\DIC\LiveVoting\Exception\DICException;

/**
 * Class ILIAS53DIC
 *
 * @package srag\DIC\LiveVoting\DIC\Implementation
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class ILIAS53DIC extends AbstractDIC {

	/**
	 * @var Container
	 */
	private $dic;


	/**
	 * ILIAS53DIC constructor
	 *
	 * @param Container $dic
	 *
	 * @internal
	 */
	public function __construct(Container $dic) {
		parent::__construct();

		$this->dic = $dic;
	}


	/**
	 * @inheritdoc
	 */
	public function access()/*: ilAccess*/ {
		return $this->dic->access();
	}


	/**
	 * @inheritdoc
	 */
	public function appEventHandler()/*: ilAppEventHandler*/ {
		return $this->dic->event();
	}


	/**
	 * @inheritdoc
	 */
	public function authSession()/*: ilAuthSession*/ {
		return $this->dic["ilAuthSession"];
	}


	/**
	 * @inheritdoc
	 */
	public function backgroundTasks()/*: BackgroundTaskServices*/ {
		return $this->dic->backgroundTasks();
	}


	/**
	 * @inheritdoc
	 */
	public function benchmark()/*: ilBenchmark*/ {
		return $this->dic["ilBench"];
	}


	/**
	 * @inheritdoc
	 */
	public function browser()/*: ilBrowser*/ {
		return $this->dic["ilBrowser"];
	}


	/**
	 * @inheritdoc
	 */
	public function clientIni()/*: ilIniFile*/ {
		return $this->dic["ilClientIniFile"];
	}


	/**
	 * @inheritdoc
	 */
	public function collator()/*: Collator*/ {
		return $this->dic["ilCollator"];
	}


	/**
	 * @inheritdoc
	 */
	public function conditions()/*: ilConditionService*/ {
		throw new DICException("ilConditionService not exists in ILIAS 5.3 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function ctrl()/*: ilCtrl*/ {
		return $this->dic->ctrl();
	}


	/**
	 * @inheritdoc
	 */
	public function ctrlStructureReader()/*: ilCtrlStructureReader*/ {
		return $this->dic["ilCtrlStructureReader"];
	}


	/**
	 * @inheritdoc
	 */
	public function database()/*: ilDBInterface*/ {
		return $this->dic->database();
	}


	/**
	 * @inheritdoc
	 */
	public function error()/*: ilErrorHandling*/ {
		return $this->dic["ilErr"];
	}


	/**
	 * @inheritdoc
	 */
	public function filesystem()/*: Filesystems*/ {
		return $this->dic->filesystem();
	}


	/**
	 * @inheritdoc
	 */
	public function help()/*: ilHelpGUI*/ {
		return $this->dic["ilHelp"];
	}


	/**
	 * @inheritdoc
	 */
	public function history()/*: ilNavigationHistory*/ {
		return $this->dic["ilNavigationHistory"];
	}


	/**
	 * @inheritdoc
	 */
	public function http()/*: HTTPServices*/ {
		return $this->dic->http();
	}


	/**
	 * @inheritdoc
	 */
	public function ilias()/*: ILIAS*/ {
		return $this->dic["ilias"];
	}


	/**
	 * @inheritdoc
	 */
	public function iliasIni()/*: ilIniFile*/ {
		return $this->dic["ilIliasIniFile"];
	}


	/**
	 * @inheritdoc
	 */
	public function language()/*: ilLanguage*/ {
		return $this->dic->language();
	}


	/**
	 * @inheritdoc
	 */
	public function learningHistory()/*: ilLearningHistoryService*/ {
		throw new DICException("ilLearningHistoryService not exists in ILIAS 5.3 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function locator()/*: ilLocatorGUI*/ {
		return $this->dic["ilLocator"];
	}


	/**
	 * @inheritdoc
	 */
	public function log()/*: ilLog*/ {
		return $this->dic["ilLog"];
	}


	/**
	 * @inheritdoc
	 */
	public function logger()/*: LoggingServices*/ {
		return $this->dic->logger();
	}


	/**
	 * @inheritdoc
	 */
	public function loggerFactory()/*: ilLoggerFactory*/ {
		return $this->dic["ilLoggerFactory"];
	}


	/**
	 * @inheritdoc
	 */
	public function mailMimeSenderFactory()/*: ilMailMimeSenderFactory*/ {
		return $this->dic["mail.mime.sender.factory"];
	}


	/**
	 * @inheritdoc
	 */
	public function mailMimeTransportFactory()/*: ilMailMimeTransportFactory*/ {
		return $this->dic["mail.mime.transport.factory"];
	}


	/**
	 * @inheritdoc
	 */
	public function mainMenu()/*: ilMainMenuGUI*/ {
		return $this->dic["ilMainMenu"];
	}


	/**
	 * @inheritdoc
	 */
	public function mainTemplate()/*: ilTemplate*/ {
		return $this->dic->ui()->mainTemplate();
	}


	/**
	 * @inheritdoc
	 */
	public function news()/*: ilNewsService*/ {
		throw new DICException("ilNewsService not exists in ILIAS 5.3 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function objDataCache()/*: ilObjectDataCache*/ {
		return $this->dic["ilObjDataCache"];
	}


	/**
	 * @inheritdoc
	 */
	public function objDefinition()/*: ilObjectDefinition*/ {
		return $this->dic["objDefinition"];
	}


	/**
	 * @inheritdoc
	 */
	public function object()/*: ilObjectService*/ {
		throw new DICException("ilObjectService not exists in ILIAS 5.3 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function pluginAdmin()/*: ilPluginAdmin*/ {
		return $this->dic["ilPluginAdmin"];
	}


	/**
	 * @inheritdoc
	 */
	public function question()/*: AsqFactory*/ {
		throw new DICException("AsqFactory not exists in ILIAS 5.4 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function rbacadmin()/*: ilRbacAdmin*/ {
		return $this->dic->rbac()->admin();
	}


	/**
	 * @inheritdoc
	 */
	public function rbacreview()/*: ilRbacReview*/ {
		return $this->dic->rbac()->review();
	}


	/**
	 * @inheritdoc
	 */
	public function rbacsystem()/*: ilRbacSystem*/ {
		return $this->dic->rbac()->system();
	}


	/**
	 * @inheritdoc
	 */
	public function session()/*: Session*/ {
		return $this->dic["sess"];
	}


	/**
	 * @inheritdoc
	 */
	public function settings()/*: ilSetting*/ {
		return $this->dic->settings();
	}


	/**
	 * @inheritdoc
	 */
	public function systemStyle()/*: ilStyleDefinition*/ {
		return $this->dic["styleDefinition"];
	}


	/**
	 * @inheritdoc
	 */
	public function tabs()/*: ilTabsGUI*/ {
		return $this->dic->tabs();
	}


	/**
	 * @inheritdoc
	 */
	public function toolbar()/*: ilToolbarGUI*/ {
		return $this->dic->toolbar();
	}


	/**
	 * @inheritdoc
	 */
	public function tree()/*: ilTree*/ {
		return $this->dic->repositoryTree();
	}


	/**
	 * @inheritdoc
	 */
	public function ui()/*: UIServices*/ {
		return $this->dic->ui();
	}


	/**
	 * @inheritdoc
	 */
	public function upload()/*: FileUpload*/ {
		return $this->dic->upload();
	}


	/**
	 * @inheritdoc
	 */
	public function user()/*: ilObjUser*/ {
		return $this->dic->user();
	}


	/**
	 * @return Container
	 */
	public function dic()/*: Container*/ {
		return $this->dic;
	}
}
