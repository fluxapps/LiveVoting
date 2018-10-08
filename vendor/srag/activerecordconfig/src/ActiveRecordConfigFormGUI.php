<?php

namespace srag\ActiveRecordConfig;

use ilPropertyFormGUI;
use srag\DIC\DICTrait;

/**
 * Class ActiveRecordConfigFormGUI
 *
 * @package srag\ActiveRecordConfig
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class ActiveRecordConfigFormGUI extends ilPropertyFormGUI {

	use DICTrait;
	/**
	 * @var ActiveRecordConfigGUI
	 */
	protected $parent;


	/**
	 * ActiveRecordConfigFormGUI constructor
	 *
	 * @param ActiveRecordConfigGUI $parent
	 */
	public function __construct(ActiveRecordConfigGUI $parent) {
		parent::__construct();

		$this->parent = $parent;

		$this->setForm();
	}


	/**
	 *
	 */
	protected function setForm()/*: void*/ {
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));

		$this->setTitle($this->txt("configuration"));

		$this->addCommandButton(ActiveRecordConfigGUI::CMD_UPDATE_CONFIGURE, $this->txt("save"));
	}


	/**
	 *
	 */
	public abstract function updateConfig()/*: void*/
	;


	/**
	 * @param string $key
	 *
	 * @return string
	 */
	private final function txt(/*string*/
		$key)/*: string*/ {
		return self::plugin()->translate($key, "activerecordconfig");
	}
}
