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
	 * @var string
	 */
	protected $tab_id;


	/**
	 * ActiveRecordConfigFormGUI constructor
	 *
	 * @param ActiveRecordConfigGUI $parent
	 * @param string                $tab_id
	 */
	public function __construct(ActiveRecordConfigGUI $parent, /*string*/
		$tab_id) {
		parent::__construct();

		$this->parent = $parent;
		$this->tab_id = $tab_id;

		$this->initForm();
	}


	/**
	 *
	 */
	protected function initForm()/*: void*/ {
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));

		$this->setTitle($this->txt($this->tab_id));

		$this->addCommandButton(ActiveRecordConfigGUI::CMD_UPDATE_CONFIGURE . "_" . $this->tab_id, $this->txt("save"));
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
	protected final function txt(/*string*/
		$key)/*: string*/ {
		return self::plugin()->translate($key, ActiveRecordConfigGUI::LANG_MODULE_CONFIG);
	}
}
