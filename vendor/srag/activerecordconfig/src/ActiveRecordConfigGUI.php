<?php

namespace srag\ActiveRecordConfig;

use ilPluginConfigGUI;
use ilUtil;
use srag\ActiveRecordConfig\Exception\ActiveRecordConfigException;
use srag\DIC\DICTrait;

/**
 * Class ActiveRecordConfigGUI
 *
 * @package srag\ActiveRecordConfig
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class ActiveRecordConfigGUI extends ilPluginConfigGUI {

	use DICTrait;
	/**
	 * @var string
	 */
	const CMD_CONFIGURE = "configure";
	/**
	 * @var string
	 */
	const CMD_UPDATE_CONFIGURE = "updateConfigure";
	/**
	 * @var string
	 *
	 * @abstract
	 */
	const CONFIG_FORM_GUI_CLASS_NAME = "";


	/**
	 * ActiveRecordConfigGUI constructor
	 */
	public function __construct() {

	}


	/**
	 * @param string $cmd
	 */
	public function performCommand(/*string*/
		$cmd)/*: void*/ {
		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			default:
				switch ($cmd) {
					case self::CMD_CONFIGURE:
					case self::CMD_UPDATE_CONFIGURE:
						$this->$cmd();
						break;

					default:
						break;
				}
				break;
		}
	}


	/**
	 * @return ActiveRecordConfigFormGUI
	 *
	 * @throws ActiveRecordConfigException Your class needs to implement the CONFIG_FORM_GUI_CLASS_NAME constant!
	 * @throws ActiveRecordConfigException Class $config_form_gui_class_name not exists!
	 * @throws ActiveRecordConfigException Class $config_form_gui_class_name not extends ActiveRecordConfigFormGUI!
	 */
	protected final function getConfigurationForm()/*: ActiveRecordConfigFormGUI*/ {
		self::checkConfigFormGuiClassNameConst();

		$config_form_gui_class_name = static::CONFIG_FORM_GUI_CLASS_NAME;

		if (!class_exists($config_form_gui_class_name)) {
			throw new ActiveRecordConfigException("Class $config_form_gui_class_name not exists!");
		}

		$config_form_gui = new $config_form_gui_class_name($this);

		if (!$config_form_gui instanceof ActiveRecordConfigFormGUI) {
			throw new ActiveRecordConfigException("Class $config_form_gui_class_name not extends ActiveRecordConfigFormGUI!");
		}

		return $config_form_gui;
	}


	/**
	 * @throws ActiveRecordConfigException Your class needs to implement the CONFIG_FORM_GUI_CLASS_NAME constant!
	 * @throws ActiveRecordConfigException Class $config_form_gui_class_name not exists!
	 * @throws ActiveRecordConfigException Class $config_form_gui_class_name not extends ActiveRecordConfigFormGUI!
	 */
	protected function configure()/*: void*/ {
		$form = $this->getConfigurationForm();

		self::plugin()->output($form);
	}


	/**
	 * @throws ActiveRecordConfigException Your class needs to implement the CONFIG_FORM_GUI_CLASS_NAME constant!
	 * @throws ActiveRecordConfigException Class $config_form_gui_class_name not exists!
	 * @throws ActiveRecordConfigException Class $config_form_gui_class_name not extends ActiveRecordConfigFormGUI!
	 */
	protected function updateConfigure()/*: void*/ {
		$form = $this->getConfigurationForm();
		$form->setValuesByPost();

		if (!$form->checkInput()) {
			self::plugin()->output($form);

			return;
		}

		$form->updateConfig();

		ilUtil::sendSuccess($this->txt("configuration_saved"));

		self::plugin()->output($form);
	}


	/**
	 * @param string $key
	 *
	 * @return string
	 */
	private final function txt(/*string*/
		$key)/*: string*/ {
		return self::plugin()->translate($key, "activerecordconfig");
	}


	/**
	 * @throws ActiveRecordConfigException Your class needs to implement the CONFIG_FORM_GUI_CLASS_NAME constant!
	 */
	private static final function checkConfigFormGuiClassNameConst()/*: void*/ {
		if (!defined("static::CONFIG_FORM_GUI_CLASS_NAME") || empty(static::CONFIG_FORM_GUI_CLASS_NAME)) {
			throw new ActiveRecordConfigException("Your class needs to implement the CONFIG_FORM_GUI_CLASS_NAME constant!");
		}
	}
}
