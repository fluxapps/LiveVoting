<?php

namespace srag\ActiveRecordConfig;

use ilCSVWriter;
use ilExcel;
use ilTable2GUI;
use srag\DIC\DICTrait;

/**
 * Class ActiveRecordConfigTableGUI
 *
 * @package srag\ActiveRecordConfig
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class ActiveRecordConfigTableGUI extends ilTable2GUI {

	use DICTrait;
	/**
	 * @var string
	 */
	protected $tab_id;


	/**
	 * ActiveRecordConfigTableGUI constructor
	 *
	 * @param ActiveRecordConfigGUI $parent
	 * @param string                $parent_cmd
	 * @param string                $tab_id
	 */
	public function __construct(ActiveRecordConfigGUI $parent, /*string*/
		$parent_cmd, /*string*/
		$tab_id) {
		parent::__construct($parent, $parent_cmd);

		$this->tab_id = $tab_id;

		if (!(strpos($parent_cmd, ActiveRecordConfigGUI::CMD_APPLY_FILTER) === 0
			|| strpos($parent_cmd, ActiveRecordConfigGUI::CMD_RESET_FILTER) === 0)) {
			$this->initTable();
		} else {
			$this->initFilter();
		}
	}


	/**
	 *
	 */
	protected function initTable()/*: void*/ {
		$parent = $this->getParentObject();

		$this->setFormAction(self::dic()->ctrl()->getFormAction($parent));

		$this->setTitle($this->txt($this->tab_id));

		$this->initFilter();

		$this->initData();

		$this->initColumns();

		$this->initExport();
		//$this->setRowTemplate("template.html", self::plugin()->directory());
	}


	/**
	 *
	 */
	public function initFilter()/*: void*/ {
		$this->setFilterCommand(ActiveRecordConfigGUI::CMD_APPLY_FILTER . "_" . $this->tab_id);
		$this->setResetCommand(ActiveRecordConfigGUI::CMD_RESET_FILTER . "_" . $this->tab_id);
	}


	/**
	 *
	 */
	protected abstract function initData()/*: void*/
	;


	/**
	 *
	 */
	protected abstract function initColumns()/*: void*/
	;


	/**
	 *
	 */
	protected function initExport()/*: void*/ {

	}


	/**
	 * @param array $row
	 */
	protected /*abstract*/
	function fillRow(/*array*/
		$row) {

	}


	/**
	 * @param ilCSVWriter $csv
	 */
	protected function fillHeaderCSV( /*ilCSVWriter*/
		$csv) {
		parent::fillHeaderCSV($csv);
	}


	/**
	 * @param ilCSVWriter $csv
	 * @param array       $result
	 */
	protected function fillRowCSV(/*ilCSVWriter*/
		$csv, /*array*/
		$result) {
		parent::fillRowCSV($csv, $result);
	}


	/**
	 * @param ilExcel $excel
	 * @param int     $row
	 */
	protected function fillHeaderExcel(ilExcel $excel, /*int*/
		&$row) {
		parent::fillHeaderExcel($excel, $row);
	}


	/**
	 * @param ilExcel $excel
	 * @param int     $row
	 * @param array   $result
	 */
	protected function fillRowExcel(ilExcel $excel, /*int*/
		&$row, /*array*/
		$result) {
		parent::fillRowExcel($excel, $row, $result);
	}


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
