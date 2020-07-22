<?php

namespace srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI;

use ilFormPropertyGUI;
use ilTableFilterItem;
use ilTemplate;
use ilToolbarItem;
use srag\CustomInputGUIs\LiveVoting\Template\Template;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class MultiSelectSearchNewInputGUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MultiSelectSearchNewInputGUI extends ilFormPropertyGUI implements ilTableFilterItem, ilToolbarItem
{

    use DICTrait;

    const EMPTY_PLACEHOLDER = "__empty_placeholder__";
    /**
     * @var bool
     */
    protected static $init = false;


    /**
     *
     */
    public static function init()/*: void*/
    {
        if (self::$init === false) {
            self::$init = true;

            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

            self::dic()->ui()->mainTemplate()->addCss($dir . "/../../node_modules/select2/dist/css/select2.min.css");

            self::dic()->ui()->mainTemplate()->addCss($dir . "/css/multi_select_search_new_input_gui.css");

            self::dic()->ui()->mainTemplate()->addJavaScript($dir . "/../../node_modules/select2/dist/js/select2.full.min.js");

            self::dic()->ui()->mainTemplate()->addJavaScript($dir . "/../../node_modules/select2/dist/js/i18n/" . self::dic()->user()->getCurrentLanguage()
                . ".js");
        }
    }


    /**
     * @var AbstractAjaxAutoCompleteCtrl|null
     */
    protected $ajax_auto_complete_ctrl = null;
    /**
     * @var int|null
     */
    protected $limit_count = null;
    /**
     * @var int|null
     */
    protected $minimum_input_length = null;
    /**
     * @var array
     */
    protected $options = [];
    /**
     * @var array
     */
    protected $value = [];


    /**
     * MultiSelectSearchNewInputGUI constructor
     *
     * @param string $title
     * @param string $post_var
     */
    public function __construct(string $title = "", string $post_var = "")
    {
        parent::__construct($title, $post_var);

        self::init();
    }


    /**
     * @param string $key
     * @param mixed  $value
     */
    public function addOption(string $key, $value)/*:void*/
    {
        $this->options[$key] = $value;
    }


    /**
     * @inheritDoc
     */
    public function checkInput() : bool
    {
        $values = $_POST[$this->getPostVar()];
        if (!is_array($values)) {
            $values = [];
        }

        $values = $this->cleanValues($values);

        if ($this->getRequired() && empty($values)) {
            $this->setAlert(self::dic()->language()->txt("msg_input_is_required"));

            return false;
        }

        if ($this->getLimitCount() !== null && count($values) > $this->getLimitCount()) {
            $this->setAlert(self::dic()->language()->txt("form_input_not_valid"));

            return false;
        }

        if ($this->getAjaxAutoCompleteCtrl() !== null) {
            if (!$this->getAjaxAutoCompleteCtrl()->validateOptions($values)) {
                $this->setAlert(self::dic()->language()->txt("form_input_not_valid"));

                return false;
            }
        } else {
            foreach ($values as $key => $value) {
                if (!isset($this->getOptions()[$value])) {
                    $this->setAlert(self::dic()->language()->txt("form_input_not_valid"));

                    return false;
                }
            }
        }

        return true;
    }


    /**
     * @param array $values
     *
     * @return array
     */
    protected function cleanValues(array $values) : array
    {
        return array_values(array_filter($values, function ($value) : bool {
            return ($value !== self::EMPTY_PLACEHOLDER);
        }));
    }


    /**
     * @return AbstractAjaxAutoCompleteCtrl|null
     */
    public function getAjaxAutoCompleteCtrl()/*: ?AbstractAjaxAutoCompleteCtrl*/
    {
        return $this->ajax_auto_complete_ctrl;
    }


    /**
     * @return int|null
     */
    public function getLimitCount()/* : ?int*/
    {
        return $this->limit_count;
    }


    /**
     * @return int
     */
    public function getMinimumInputLength() : int
    {
        if ($this->minimum_input_length !== null) {
            return $this->minimum_input_length;
        } else {
            return ($this->getAjaxAutoCompleteCtrl() !== null ? 3 : 0);
        }
    }


    /**
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }


    /**
     * @inheritDoc
     */
    public function getTableFilterHTML() : string
    {
        return $this->render();
    }


    /**
     * @inheritDoc
     */
    public function getToolbarHTML() : string
    {
        return $this->render();
    }


    /**
     * @return array
     */
    public function getValue() : array
    {
        return $this->value;
    }


    /**
     * @param ilTemplate $tpl
     */
    public function insert(ilTemplate $tpl) /*: void*/
    {
        $html = $this->render();

        $tpl->setCurrentBlock("prop_generic");
        $tpl->setVariable("PROP_GENERIC", $html);
        $tpl->parseCurrentBlock();
    }


    /**
     * @return string
     */
    public function render() : string
    {
        $tpl = new Template(__DIR__ . "/templates/multi_select_search_new_input_gui.html");

        $tpl->setVariableEscaped("ID", $this->getFieldId());

        $tpl->setVariableEscaped("POST_VAR", $this->getPostVar());

        $tpl->setVariableEscaped("EMPTY_PLACEHOLDER", self::EMPTY_PLACEHOLDER); // ILIAS 6 will not set `null` value to input on post

        $config = [
            "maximumSelectionLength" => $this->getLimitCount(),
            "minimumInputLength"     => $this->getMinimumInputLength()
        ];
        if ($this->getAjaxAutoCompleteCtrl() !== null) {
            $config["ajax"] = [
                "delay" => 500,
                "url"   => self::dic()->ctrl()->getLinkTarget($this->getAjaxAutoCompleteCtrl(), AbstractAjaxAutoCompleteCtrl::CMD_AJAX_AUTO_COMPLETE, "", true, false)
            ];

            $options = $this->getAjaxAutoCompleteCtrl()->fillOptions($this->getValue());
        } else {
            $options = $this->getOptions();
        }

        $tpl->setVariableEscaped("CONFIG", base64_encode(json_encode($config)));

        if (!empty($options)) {

            $tpl->setCurrentBlock("option");

            foreach ($options as $option_value => $option_text) {
                $selected = in_array($option_value, $this->getValue());

                if ($selected) {
                    $tpl->setVariableEscaped("SELECTED", "selected");
                }

                $tpl->setVariableEscaped("VAL", $option_value);
                $tpl->setVariableEscaped("TEXT", $option_text);

                $tpl->parseCurrentBlock();
            }
        }

        return self::output()->getHTML($tpl);
    }


    /**
     * @param AbstractAjaxAutoCompleteCtrl|null $ajax_auto_complete_ctrl
     */
    public function setAjaxAutoCompleteCtrl(/*?*/ AbstractAjaxAutoCompleteCtrl $ajax_auto_complete_ctrl = null)/*: void*/
    {
        $this->ajax_auto_complete_ctrl = $ajax_auto_complete_ctrl;
    }


    /**
     * @param int|null $limit_count
     */
    public function setLimitCount(/*?*/ int $limit_count = null)/* : void*/
    {
        $this->limit_count = $limit_count;
    }


    /**
     * @param int|null $minimum_input_length
     */
    public function setMinimumInputLength(/*?*/ int $minimum_input_length = null)/*: void*/
    {
        $this->minimum_input_length = $minimum_input_length;
    }


    /**
     * @param array $options
     */
    public function setOptions(array $options)/* : void*/
    {
        $this->options = $options;
    }


    /**
     * @param array $value
     */
    public function setValue(/*array*/ $value)/*: void*/
    {
        if (is_array($value)) {
            $this->value = $this->cleanValues($value);
        } else {
            $this->value = [];
        }
    }


    /**
     * @param array $value
     */
    public function setValueByArray(/*array*/ $value)/*: void*/
    {
        $this->setValue($value[$this->getPostVar()]);
    }
}
