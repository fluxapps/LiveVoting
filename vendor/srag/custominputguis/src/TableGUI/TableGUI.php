<?php

namespace srag\CustomInputGUIs\LiveVoting\TableGUI;

use ilCSVWriter;
use ilExcel;
use ilFormPropertyGUI;
use ilHtmlToPdfTransformerFactory;
use ilTable2GUI;
use srag\CustomInputGUIs\LiveVoting\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\LiveVoting\TableGUI\Exception\TableGUIException;
use srag\CustomInputGUIs\LiveVoting\Template\Template;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class TableGUI
 *
 * @package    srag\CustomInputGUIs\LiveVoting\TableGUI
 *
 * @deprecated Please use "srag/datatable" library (`AbstractTableBuilder`)
 */
abstract class TableGUI extends ilTable2GUI
{

    use DICTrait;

    /**
     * @var int
     *
     * @deprecated
     */
    const DEFAULT_FORMAT = 0;
    /**
     * @var int
     *
     * @deprecated
     */
    const EXPORT_PDF = 3;
    /**
     * @var string
     *
     * @deprecated
     */
    const LANG_MODULE = "";
    /**
     * @var string
     *
     * @abstract
     *
     * @deprecated
     */
    const ROW_TEMPLATE = "";
    /**
     * @var array
     *
     * @deprecated
     */
    protected $filter_fields = [];
    /**
     * @var Template
     *
     * @deprecated
     */
    protected $tpl;
    /**
     * @var ilFormPropertyGUI[]
     *
     * @deprecated
     */
    private $filter_cache = [];


    /**
     * TableGUI constructor
     *
     * @param object $parent
     * @param string $parent_cmd
     *
     * @deprecated
     */
    public function __construct(/*object*/ $parent, string $parent_cmd)
    {
        $this->parent_obj = $parent;
        $this->parent_cmd = $parent_cmd;

        $this->initId();

        parent::__construct($parent, $parent_cmd);

        $this->initTable();
    }


    /**
     * @inheritDoc
     *
     * @param int  $format
     * @param bool $send
     *
     * @deprecated
     */
    public function exportData(/*int*/ $format, /*bool*/ $send = false)/*: void*/
    {
        switch ($format) {
            case self::EXPORT_PDF:
                $this->exportPDF($format);
                break;

            default:
                parent::exportData($format, $send);
                break;
        }
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function fillFooter()/*: void*/
    {
        parent::fillFooter();
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function fillHeader()/*: void*/
    {
        parent::fillHeader();
    }


    /**
     * @inheritDoc
     *
     * @return array
     *
     * @deprecated
     */
    public final function getSelectableColumns() : array
    {
        return array_map(function (array &$column) : array {
            if (!isset($column["txt"])) {
                $column["txt"] = $this->txt($column["id"]);
            }

            return $column;
        }, $this->getSelectableColumns2());
    }


    /**
     * @inheritDoc
     *
     * @throws TableGUIException $filters needs to be an array!
     * @throws TableGUIException $field needs to be an array!
     *
     * @deprecated
     */
    public final function initFilter()/*: void*/
    {
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

            if ($item instanceof MultiLineNewInputGUI) {
                if (is_array($field[PropertyFormGUI::PROPERTY_SUBITEMS])) {
                    foreach ($field[PropertyFormGUI::PROPERTY_SUBITEMS] as $child_key => $child_field) {
                        if (!is_array($child_field)) {
                            throw new TableGUIException("\$fields needs to be an array!", TableGUIException::CODE_INVALID_FIELD);
                        }

                        if ($child_field[PropertyFormGUI::PROPERTY_NOT_ADD]) {
                            continue;
                        }

                        $child_item = Items::getItem($child_key, $child_field, $item, $this);

                        $item->addInput($child_item);
                    }
                }
            }

            $this->filter_cache[$key] = $item;

            $this->addFilterItem($item);

            if ($this->hasSessionValue($item->getFieldId())) { // Supports filter default values
                $item->readFromSession();
            }
        }
    }


    /**
     * @inheritDoc
     *
     * @param string $col
     *
     * @return bool
     *
     * @deprecated
     */
    public function isColumnSelected(/*string*/ $col) : bool
    {
        return parent::isColumnSelected($col);
    }


    /**
     * @inheritDoc
     *
     * @param array $formats
     *
     * @deprecated
     */
    public function setExportFormats(array $formats)/*: void*/
    {
        parent::setExportFormats($formats);

        $valid = [self::EXPORT_PDF => "pdf"];

        foreach ($formats as $format) {
            if (isset($valid[$format])) {
                $this->export_formats[$format] = self::plugin()->getPluginObject()->getPrefix() . "_tablegui_export_" . $valid[$format];
            }
        }
    }


    /**
     * @param string      $key
     * @param string|null $default
     *
     * @return string
     *
     * @deprecated
     */
    public function txt(string $key,/*?*/ string $default = null) : string
    {
        if ($default !== null) {
            return self::plugin()->translate($key, static::LANG_MODULE, [], true, "", $default);
        } else {
            return self::plugin()->translate($key, static::LANG_MODULE);
        }
    }


    /**
     * @param bool $send
     *
     * @deprecated
     */
    protected function exportPDF(bool $send = false)/*: void*/
    {

        $css = file_get_contents(__DIR__ . "/css/table_pdf_export.css");

        $tpl = new Template(__DIR__ . "/templates/table_pdf_export.html");

        $tpl->setVariable("CSS", $css);

        $tpl->setCurrentBlock("header");
        foreach ($this->fillHeaderPDF() as $column) {
            $tpl->setVariable("HEADER", $column);

            $tpl->parseCurrentBlock();
        }

        $tpl->setCurrentBlock("body");
        foreach ($this->row_data as $row) {
            $tpl_row = new Template(__DIR__ . "/templates/table_pdf_export_row.html");

            $tpl_row->setCurrentBlock("row");

            foreach ($this->fillRowPDF($row) as $column) {
                $tpl_row->setVariable("COLUMN", $column);

                $tpl_row->parseCurrentBlock();
            }

            $tpl->setVariable("ROW", self::output()->getHTML($tpl_row));

            $tpl->parseCurrentBlock();
        }

        $html = self::output()->getHTML($tpl);

        $a = new ilHtmlToPdfTransformerFactory();
        $a->deliverPDFFromHTMLString($html, "export.pdf", $send ? ilHtmlToPdfTransformerFactory::PDF_OUTPUT_DOWNLOAD : ilHtmlToPdfTransformerFactory::PDF_OUTPUT_FILE, static::PLUGIN_CLASS_NAME, "");
    }


    /**
     * @inheritDoc
     *
     * @param ilCSVWriter $csv
     *
     * @deprecated
     */
    protected function fillHeaderCSV(/*ilCSVWriter*/ $csv)/*: void*/
    {
        foreach ($this->getSelectableColumns() as $column) {
            if ($this->isColumnSelected($column["id"])) {
                $csv->addColumn($column["txt"]);
            }
        }

        $csv->addRow();
    }


    /**
     * @inheritDoc
     *
     * @param ilExcel $excel
     * @param int     $row
     *
     * @deprecated
     */
    protected function fillHeaderExcel(ilExcel $excel, /*int*/ &$row)/*: void*/
    {
        $col = 0;

        foreach ($this->getSelectableColumns() as $column) {
            if ($this->isColumnSelected($column["id"])) {
                $excel->setCell($row, $col, $column["txt"]);
                $col++;
            }
        }

        if ($col > 0) {
            $excel->setBold("A" . $row . ":" . $excel->getColumnCoord($col - 1) . $row);
        }
    }


    /**
     * @return array
     *
     * @deprecated
     */
    protected function fillHeaderPDF() : array
    {
        $columns = [];

        foreach ($this->getSelectableColumns() as $column) {
            if ($this->isColumnSelected($column["id"])) {
                $columns[] = $column["txt"];
            }
        }

        return $columns;
    }


    /**
     * @inheritDoc
     *
     * @param array|object $row
     *
     * @deprecated
     */
    protected function fillRow(/*array*/ $row)/*: void*/
    {
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
     * @inheritDoc
     *
     * @param ilCSVWriter  $csv
     * @param array|object $row
     *
     * @deprecated
     */
    protected function fillRowCSV(/*ilCSVWriter*/ $csv, /*array*/ $row)/*: void*/
    {
        foreach ($this->getSelectableColumns() as $column) {
            if ($this->isColumnSelected($column["id"])) {
                $csv->addColumn($this->getColumnValue($column["id"], $row, self::EXPORT_CSV));
            }
        }

        $csv->addRow();
    }


    /**
     * @inheritDoc
     *
     * @param ilExcel      $excel
     * @param int          $row
     * @param array|object $result
     *
     * @deprecated
     */
    protected function fillRowExcel(ilExcel $excel, /*int*/ &$row, /*array*/ $result)/*: void*/
    {
        $col = 0;
        foreach ($this->getSelectableColumns() as $column) {
            if ($this->isColumnSelected($column["id"])) {
                $excel->setCell($row, $col, $this->getColumnValue($column["id"], $result, self::EXPORT_EXCEL));
                $col++;
            }
        }
    }


    /**
     * @param array $row
     *
     * @return array
     *
     * @deprecated
     */
    protected function fillRowPDF(/*array*/ $row) : array
    {
        $strings = [];

        foreach ($this->getSelectableColumns() as $column) {
            if ($this->isColumnSelected($column["id"])) {
                $strings[] = $this->getColumnValue($column["id"], $row, self::EXPORT_PDF);
            }
        }

        return $strings;
    }


    /**
     * @param string       $column
     * @param array|object $row
     * @param int          $format
     *
     * @return string
     *
     * @deprecated
     */
    protected abstract function getColumnValue(string $column, /*array*/ $row, int $format = self::DEFAULT_FORMAT) : string;


    /**
     * @return array
     *
     * @deprecated
     */
    protected final function getFilterValues() : array
    {
        return array_map(function ($item) {
            return Items::getValueFromItem($item);
        }, $this->filter_cache);
    }


    /**
     * @return array
     *
     * @deprecated
     */
    protected abstract function getSelectableColumns2() : array;


    /**
     * @param string $field_id
     *
     * @return bool
     *
     * @deprecated
     */
    protected final function hasSessionValue(string $field_id) : bool
    {
        // Not set (null) on first visit, false on reset filter, string if is set
        return (isset($_SESSION["form_" . $this->getId()][$field_id]) && $_SESSION["form_" . $this->getId()][$field_id] !== false);
    }


    /**
     * @deprecated
     */
    protected function initAction()/*: void*/
    {
        $this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent_obj));
    }


    /**
     * @deprecated
     */
    protected function initColumns()/*: void*/
    {
        foreach ($this->getSelectableColumns() as $column) {
            if ($this->isColumnSelected($column["id"])) {
                $this->addColumn($column["txt"], ($column["sort"] ? $column["id"] : null));
            }
        }
    }


    /**
     * @deprecated
     */
    protected function initCommands()/*: void*/
    {

    }


    /**
     * @deprecated
     */
    protected abstract function initData()/*: void*/ ;


    /**
     * @deprecated
     */
    protected function initExport()/*: void*/
    {

    }


    /**
     * @deprecated
     */
    protected abstract function initFilterFields()/*: void*/ ;


    /**
     * @deprecated
     */
    protected abstract function initId()/*: void*/ ;


    /**
     * @deprecated
     */
    protected abstract function initTitle()/*: void*/ ;


    /**
     * @return bool
     *
     * @deprecated
     */
    private final function checkRowTemplateConst() : bool
    {
        return (defined("static::ROW_TEMPLATE") && !empty(static::ROW_TEMPLATE));
    }


    /**
     * @deprecated
     */
    private final function initRowTemplate()/*: void*/
    {
        if ($this->checkRowTemplateConst()) {
            $this->setRowTemplate(static::ROW_TEMPLATE, self::plugin()->directory());
        } else {
            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);
            $this->setRowTemplate("table_row.html", $dir);
        }
    }


    /**
     * @deprecated
     */
    private final function initTable()/*: void*/
    {
        if (!(strpos($this->parent_cmd, "applyFilter") === 0
            || strpos($this->parent_cmd, "resetFilter") === 0)
        ) {
            $this->tpl = new Template($this->tpl->lastTemplatefile, $this->tpl->removeUnknownVariables, $this->tpl->removeEmptyBlocks);

            $this->initAction();

            $this->initTitle();

            $this->initFilter();

            $this->initData();

            $this->initColumns();

            $this->initExport();

            $this->initRowTemplate();

            $this->initCommands();
        } else {
            // Speed up, not init data on applyFilter or resetFilter, only filter
            $this->initFilter();
        }
    }
}
