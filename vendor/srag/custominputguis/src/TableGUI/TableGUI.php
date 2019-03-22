<?php

namespace srag\CustomInputGUIs\LiveVoting\TableGUI;

use ilCSVWriter;
use ilExcel;
use ilFormPropertyGUI;
use ilTable2GUI;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\LiveVoting\TableGUI\Exception\TableGUIException;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class TableGUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\TableGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class TableGUI extends ilTable2GUI {

	use DICTrait;
	/**
	 * @var string
	 *
	 * @abstract
	 */
	const ROW_TEMPLATE = "";
	/**
	 * @var string
	 */
	const LANG_MODULE = "";
	/**
	 * @var array
	 */
	protected $filter_fields = [];
	/**
	 * @var ilFormPropertyGUI[]
	 */
	private $filter_cache = [];


	/**
	 * TableGUI constructor
	 *
	 * @param object $parent
	 * @param string $parent_cmd
	 */
	public function __construct($parent, /*string*/
		$parent_cmd) {
		$this->initId();

		parent::__construct($parent, $parent_cmd);

		if (!(strpos($parent_cmd, "applyFilter") === 0
			|| strpos($parent_cmd, "resetFilter") === 0)) {
			$this->initTable();
		} else {
			// Speed up, not init data, only filter
			$this->initFilter();
		}
	}


	/**
	 * @return array
	 */
	protected final function getFilterValues()/*: array*/ {
		return array_map(function ($item) {
			return Items::getValueFromItem($item);
		}, $this->filter_cache);
	}


	/**
	 *
	 */
	public final function getSelectableColumns() {
		return array_map(function (array &$column)/*: array*/ {
			if (!isset($column["txt"])) {
				$column["txt"] = $this->txt($column["id"]);
			}

			return $column;
		}, $this->getSelectableColumns2());
	}


	/**
	 * @param string $field_id
	 *
	 * @return bool
	 */
	protected final function hasSessionValue(/*string*/
		$field_id)/*: bool*/ {
		// Not set (null) on first visit, false on reset filter, string if is set
		return (isset($_SESSION["form_" . $this->getId()][$field_id]) && $_SESSION["form_" . $this->getId()][$field_id] !== false);
	}


	/**
	 *
	 *
	 * @throws TableGUIException $filters needs to be an array!
	 * @throws TableGUIException $field needs to be an array!
	 */
	public final function initFilter()/*: void*/ {
		$this->setDisableFilterHiding(true);

		$this->initFilterFields();

		if (!is_array($this->filter_fields)) {
			throw new TableGUIException("\$filters needs to be an array!", TableGUIException::CODE_INVALID_FIELD);
		}

		foreach ($this->filter_fields as $key => $field) {
			if (!is_array($field)) {
				throw new TableGUIException("\$field needs to be an array!", TableGUIException::CODE_INVALID_FIELD);
			}

			if ($field[PropertyFormGUI::PROPERTY_NOT_ADD]) {
				continue;
			}

			$item = Items::getItem($key, $field, $this, $this);

			/*if (!($item instanceof ilTableFilterItem)) {
				throw new TableGUIException("\$item must be an instance of ilTableFilterItem!", TableGUIException::CODE_INVALID_FIELD);
			}*/

			$this->filter_cache[$key] = $item;

			$this->addFilterItem($item);

			if ($this->hasSessionValue($item->getFieldId())) { // Supports filter default values
				$item->readFromSession();
			}
		}
	}


	/**
	 *
	 */
	private final function initRowTemplate()/*: void*/ {
		if ($this->checkRowTemplateConst()) {
			$this->setRowTemplate(static::ROW_TEMPLATE, self::plugin()->directory());
		} else {
			$dir = __DIR__;
			$dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);
			$this->setRowTemplate("table_row.html", $dir);
		}
	}


	/**
	 *
	 */
	private final function initTable()/*: void*/ {
		$this->initAction();

		$this->initTitle();

		$this->initFilter();

		$this->initData();

		$this->initColumns();

		$this->initExport();

		$this->initRowTemplate();

		$this->initCommands();
	}


	/**
	 * @param string      $key
	 * @param string|null $default
	 *
	 * @return string
	 */
	public final function txt(/*string*/
		$key,/*?string*/
		$default = NULL)/*: string*/ {
		if ($default !== NULL) {
			return self::plugin()->translate($key, static::LANG_MODULE, [], true, "", $default);
		} else {
			return self::plugin()->translate($key, static::LANG_MODULE);
		}
	}


	/**
	 * @return bool
	 */
	private final function checkRowTemplateConst()/*: bool*/ {
		return (defined("static::ROW_TEMPLATE") && !empty(static::ROW_TEMPLATE));
	}


	/**
	 *
	 */
	public function fillHeader()/*: void*/ {
		parent::fillHeader();
	}


	/**
	 * @param array $row
	 */
	protected function fillRow(/*array*/
		$row)/*: void*/ {
		$this->tpl->setCurrentBlock("column");

		foreach ($this->getSelectableColumns() as $column) {
			if ($this->isColumnSelected($column["id"])) {
				$column = $this->getColumnValue($column["id"], $row);

				if (!empty($column)) {
					$this->tpl->setVariable("COLUMN", $column);
				} else {
					$this->tpl->setVariable("COLUMN", " ");
				}

				$this->tpl->parseCurrentBlock();
			}
		}
	}


	/**
	 *
	 */
	public function fillFooter()/*: void*/ {
		parent::fillFooter();
	}


	/**
	 * @param ilCSVWriter $csv
	 */
	protected function fillHeaderCSV(/*ilCSVWriter*/
		$csv)/*: void*/ {
		foreach ($this->getSelectableColumns() as $column) {
			$csv->addColumn($column["txt"]);
		}

		$csv->addRow();
	}


	/**
	 * @param ilCSVWriter $csv
	 * @param array       $row
	 */
	protected function fillRowCSV(/*ilCSVWriter*/
		$csv, /*array*/
		$row)/*: void*/ {
		foreach ($this->getSelectableColumns() as $column) {
			if ($this->isColumnSelected($column["id"])) {
				$csv->addColumn($this->getColumnValue($column["id"], $row, true));
			}
		}

		$csv->addRow();
	}


	/**
	 * @param ilExcel $excel
	 * @param int     $row
	 */
	protected function fillHeaderExcel(ilExcel $excel, /*int*/
		&$row)/*: void*/ {
		$col = 0;

		foreach ($this->getSelectableColumns() as $column) {
			$excel->setCell($row, $col, $column["txt"]);
			$col ++;
		}

		$excel->setBold("A" . $row . ":" . $excel->getColumnCoord($col - 1) . $row);
	}


	/**
	 * @param ilExcel $excel
	 * @param int     $row
	 * @param array   $result
	 */
	protected function fillRowExcel(ilExcel $excel, /*int*/
		&$row, /*array*/
		$result)/*: void*/ {
		$col = 0;
		foreach ($this->getSelectableColumns() as $column) {
			if ($this->isColumnSelected($column["id"])) {
				$excel->setCell($row, $col, $this->getColumnValue($column["id"], $result));
				$col ++;
			}
		}
	}


	/**
	 * @param string $column
	 * @param array  $row
	 * @param bool   $raw_export
	 *
	 * @return string
	 */
	protected abstract function getColumnValue(/*string*/
		$column, /*array*/
		$row, /*bool*/
		$raw_export = false)/*: string*/
	;


	/**
	 * @return array
	 */
	protected abstract function getSelectableColumns2()/*: array*/
	;


	/**
	 *
	 */
	protected function initAction()/*: void*/ {
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent_obj));
	}


	/**
	 *
	 */
	protected function initColumns()/*: void*/ {
		foreach ($this->getSelectableColumns() as $column) {
			if ($this->isColumnSelected($column["id"])) {
				$this->addColumn($column["txt"], ($column["sort"] ? $column["id"] : NULL));
			}
		}
	}


	/**
	 *
	 */
	protected function initCommands()/*: void*/ {

	}


	/**
	 *
	 */
	protected function initExport()/*: void*/ {

	}


	/**
	 * @param string $col
	 *
	 * @return bool
	 */
	public function isColumnSelected(/*string*/
		$col)/*: bool*/ {
		return parent::isColumnSelected($col);
	}


	/**
	 *
	 */
	protected abstract function initData()/*: void*/
	;


	/**
	 *
	 */
	protected abstract function initFilterFields()/*: void*/
	;


	/**
	 *
	 */
	protected abstract function initId()/*: void*/
	;


	/**
	 *
	 */
	protected abstract function initTitle()/*: void*/
	;
}
