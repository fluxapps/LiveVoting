<?php

namespace srag\DIC;

use ilLoggerFactory;

/**
 * Class LegacyDIC
 *
 * @package srag\DIC
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
	 */
	public function __construct(array &$globals) {
		parent::__construct();

		$this->globals = &$globals;
	}


	/**
	 * @inheritdoc
	 */
	public function access() {
		return $this->globals["ilAccess"];
	}


	/**
	 * @inheritdoc
	 */
	public function appEventHandler() {
		return $this->globals["ilAppEventHandler"];
	}


	/**
	 * @inheritdoc
	 */
	public function authSession() {
		return $this->globals["ilAuthSession"];
	}


	/**
	 * @inheritdoc
	 */
	public function backgroundTasks() {
		throw new DICException("BackgroundTaskServices not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function benchmark() {
		return $this->globals["ilBench"];
	}


	/**
	 * @inheritdoc
	 */
	public function browser() {
		return $this->globals["ilBrowser"];
	}


	/**
	 * @inheritdoc
	 */
	public function clientIni() {
		return $this->globals["ilClientIniFile"];
	}


	/**
	 * @inheritdoc
	 */
	public function collator() {
		return $this->globals["ilCollator"];
	}


	/**
	 * @inheritdoc
	 */
	public function ctrl() {
		return $this->globals["ilCtrl"];
	}


	/**
	 * @inheritdoc
	 */
	public function ctrlStructureReader() {
		return $this->globals["ilCtrlStructureReader"];
	}


	/**
	 * @inheritdoc
	 */
	public function database() {
		return $this->globals["ilDB"];
	}


	/**
	 * @inheritdoc
	 */
	public function error() {
		return $this->globals["ilErr"];
	}


	/**
	 * @inheritdoc
	 */
	public function filesystem() {
		throw new DICException("Filesystems not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function help() {
		return $this->globals["ilHelp"];
	}


	/**
	 * @inheritdoc
	 */
	public function history() {
		return $this->globals["ilNavigationHistory"];
	}


	/**
	 * @inheritdoc
	 */
	public function http() {
		throw new DICException("HTTPServices not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function ilias() {
		return $this->globals["ilias"];
	}


	/**
	 * @inheritdoc
	 */
	public function iliasIni() {
		return $this->globals["ilIliasIniFile"];
	}


	/**
	 * @inheritdoc
	 */
	public function language() {
		return $this->globals["lng"];
	}


	/**
	 * @inheritdoc
	 */
	public function locator() {
		return $this->globals["ilLocator"];
	}


	/**
	 * @inheritdoc
	 */
	public function log() {
		return $this->globals["ilLog"];
	}


	/**
	 * @inheritdoc
	 */
	public function logger() {
		throw new DICException("LoggingServices not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function loggerFactory() {
		return ilLoggerFactory::getInstance();
	}


	/**
	 * @inheritdoc
	 */
	public function mailMimeSenderFactory() {
		throw new DICException("ilMailMimeSenderFactory not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function mainMenu() {
		return $this->globals["ilMainMenu"];
	}


	/**
	 * @inheritdoc
	 */
	public function objDataCache() {
		return $this->globals["ilObjDataCache"];
	}


	/**
	 * @inheritdoc
	 */
	public function objDefinition() {
		return $this->globals["objDefinition"];
	}


	/**
	 * @inheritdoc
	 */
	public function pluginAdmin() {
		return $this->globals["ilPluginAdmin"];
	}


	/**
	 * @inheritdoc
	 */
	public function rbacadmin() {
		return $this->globals["rbacadmin"];
	}


	/**
	 * @inheritdoc
	 */
	public function rbacreview() {
		return $this->globals["rbacreview"];
	}


	/**
	 * @inheritdoc
	 */
	public function rbacsystem() {
		return $this->globals["rbacsystem"];
	}


	/**
	 * @inheritdoc
	 */
	public function session() {
		return $this->globals["sess"];
	}


	/**
	 * @inheritdoc
	 */
	public function settings() {
		return $this->globals["ilSetting"];
	}


	/**
	 * @inheritdoc
	 */
	public function systemStyle() {
		return $this->globals["styleDefinition"];
	}


	/**
	 * @inheritdoc
	 */
	public function tabs() {
		return $this->globals["ilTabs"];
	}


	/**
	 * @inheritdoc
	 */
	public function template() {
		return $this->globals["tpl"];
	}


	/**
	 * @inheritdoc
	 */
	public function toolbar() {
		return $this->globals["ilToolbar"];
	}


	/**
	 * @inheritdoc
	 */
	public function tree() {
		return $this->globals["tree"];
	}


	/**
	 * @inheritdoc
	 */
	public function ui() {
		throw new DICException("UIServices not exists in ILIAS 5.1 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function upload() {
		throw new DICException("FileUpload not exists in ILIAS 5.2 or below!");
	}


	/**
	 * @inheritdoc
	 */
	public function user() {
		return $this->globals["ilUser"];
	}


	/**
	 * @return array
	 */
	public function &globals() {
		return $this->globals;
	}
}
