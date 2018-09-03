<?php

namespace srag\DIC\DIC;

use ILIAS\DI\Container;
use srag\DIC\Exception\DICException;

/**
 * Class NewDIC
 *
 * @package srag\DIC\DIC
 */
final class NewDIC extends AbstractDIC {

	/**
	 * @var Container
	 */
	private $dic;


	/**
	 * NewDIC constructor
	 *
	 * @param Container $dic
	 */
	public function __construct(Container $dic) {
		parent::__construct();

		$this->dic = $dic;
	}


	/**
	 * @inheritdoc
	 */
	public function access() {
		return $this->dic->access();
	}


	/**
	 * @inheritdoc
	 */
	public function appEventHandler() {
		return $this->dic->event();
	}


	/**
	 * @inheritdoc
	 */
	public function authSession() {
		return $this->dic["ilAuthSession"];
	}


	/**
	 * @inheritdoc
	 */
	public function backgroundTasks() {
		if ($this->is53()) {
			return $this->dic->backgroundTasks();
		} else {
			throw new DICException("BackgroundTaskServices not exists in ILIAS 5.2 or below!");
		}
	}


	/**
	 * @inheritdoc
	 */
	public function benchmark() {
		return $this->dic["ilBench"];
	}


	/**
	 * @inheritdoc
	 */
	public function browser() {
		return $this->dic["ilBrowser"];
	}


	/**
	 * @inheritdoc
	 */
	public function clientIni() {
		if ($this->is54()) {
			return $this->dic->clientIni();
		} else {
			return $this->dic["ilClientIniFile"];
		}
	}


	/**
	 * @inheritdoc
	 */
	public function collator() {
		return $this->dic["ilCollator"];
	}


	/**
	 * @inheritdoc
	 */
	public function ctrl() {
		return $this->dic->ctrl();
	}


	/**
	 * @inheritdoc
	 */
	public function ctrlStructureReader() {
		return $this->dic["ilCtrlStructureReader"];
	}


	/**
	 * @inheritdoc
	 */
	public function database() {
		return $this->dic->database();
	}


	/**
	 * @inheritdoc
	 */
	public function error() {
		return $this->dic["ilErr"];
	}


	/**
	 * @inheritdoc
	 */
	public function filesystem() {
		if ($this->is53()) {
			return $this->dic->filesystem();
		} else {
			throw new DICException("Filesystems not exists in ILIAS 5.2 or below!");
		}
	}


	/**
	 * @inheritdoc
	 */
	public function help() {
		if ($this->is54()) {
			return $this->dic->help();
		} else {
			return $this->dic["ilHelp"];
		}
	}


	/**
	 * @inheritdoc
	 */
	public function http() {
		if ($this->is53()) {
			return $this->dic->http();
		} else {
			throw new DICException("HTTPServices not exists in ILIAS 5.2 or below!");
		}
	}


	/**
	 * @inheritdoc
	 */
	public function history() {
		return $this->dic["ilNavigationHistory"];
	}


	/**
	 * @inheritdoc
	 */
	public function ilias() {
		return $this->dic["ilias"];
	}


	/**
	 * @inheritdoc
	 */
	public function iliasIni() {
		if ($this->is54()) {
			return $this->dic->iliasIni();
		} else {
			return $this->dic["ilIliasIniFile"];
		}
	}


	/**
	 * @inheritdoc
	 */
	public function language() {
		return $this->dic->language();
	}


	/**
	 * @inheritdoc
	 */
	public function locator() {
		return $this->dic["ilLocator"];
	}


	/**
	 * @inheritdoc
	 */
	public function log() {
		return $this->dic["ilLog"];
	}


	/**
	 * @inheritdoc
	 */
	public function logger() {
		return $this->dic->logger();
	}


	/**
	 * @inheritdoc
	 */
	public function loggerFactory() {
		return $this->dic["ilLoggerFactory"];
	}


	/**
	 * @inheritdoc
	 */
	public function mailMimeSenderFactory() {
		if ($this->is53()) {
			return $this->dic["mail.mime.sender.factory"];
		} else {
			throw new DICException("ilMailMimeSenderFactory not exists in ILIAS 5.2 or below!");
		}
	}


	/**
	 * @inheritdoc
	 */
	public function mainMenu() {
		return $this->dic["ilMainMenu"];
	}


	/**
	 * @inheritdoc
	 */
	public function objDataCache() {
		return $this->dic["ilObjDataCache"];
	}


	/**
	 * @inheritdoc
	 */
	public function objDefinition() {
		return $this->dic["objDefinition"];
	}


	/**
	 * @inheritdoc
	 */
	public function pluginAdmin() {
		return $this->dic["ilPluginAdmin"];
	}


	/**
	 * @inheritdoc
	 */
	public function rbacadmin() {
		return $this->dic->rbac()->admin();
	}


	/**
	 * @inheritdoc
	 */
	public function rbacreview() {
		return $this->dic->rbac()->review();
	}


	/**
	 * @inheritdoc
	 */
	public function rbacsystem() {
		return $this->dic->rbac()->system();
	}


	/**
	 * @inheritdoc
	 */
	public function session() {
		return $this->dic["sess"];
	}


	/**
	 * @inheritdoc
	 */
	public function settings() {
		return $this->dic->settings();
	}


	/**
	 * @inheritdoc
	 */
	public function systemStyle() {
		if ($this->is54()) {
			return $this->dic->systemStyle();
		} else {
			return $this->dic["styleDefinition"];
		}
	}


	/**
	 * @inheritdoc
	 */
	public function tabs() {
		return $this->dic->tabs();
	}


	/**
	 * @inheritdoc
	 */
	public function template() {
		return $this->dic->ui()->mainTemplate();
	}


	/**
	 * @inheritdoc
	 */
	public function toolbar() {
		return $this->dic->toolbar();
	}


	/**
	 * @inheritdoc
	 */
	public function tree() {
		return $this->dic->repositoryTree();
	}


	/**
	 * @inheritdoc
	 */
	public function ui() {
		return $this->dic->ui();
	}


	/**
	 * @inheritdoc
	 */
	public function upload() {
		if ($this->is53()) {
			return $this->dic->upload();
		} else {
			throw new DICException("FileUpload not exists in ILIAS 5.2 or below!");
		}
	}


	/**
	 * @inheritdoc
	 */
	public function user() {
		return $this->dic->user();
	}


	/**
	 * @return Container
	 */
	public function dic() {
		return $this->dic;
	}


	/**
	 * @return bool
	 */
	private function is53() {
		return (ILIAS_VERSION_NUMERIC >= "5.3");
	}


	/**
	 * @return bool
	 */
	private function is54() {
		return (ILIAS_VERSION_NUMERIC >= "5.4");
	}
}
