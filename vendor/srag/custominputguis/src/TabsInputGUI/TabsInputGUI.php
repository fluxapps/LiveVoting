<?php

namespace srag\CustomInputGUIs\LiveVoting\TabsInputGUI;

use ilFormPropertyGUI;
use ilTableFilterItem;
use ilTemplate;
use ilToolbarItem;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\LiveVoting\Template\Template;
use srag\DIC\LiveVoting\DICTrait;
use srag\DIC\LiveVoting\Plugin\PluginInterface;
use srag\DIC\LiveVoting\Version\PluginVersionParameter;

/**
 * Class TabsInputGUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\TabsInputGUI
 */
class TabsInputGUI extends ilFormPropertyGUI implements ilTableFilterItem, ilToolbarItem
{

    use DICTrait;

    const SHOW_INPUT_LABEL_ALWAYS = 3;
    const SHOW_INPUT_LABEL_AUTO = 2;
    const SHOW_INPUT_LABEL_NONE = 1;
    /**
     * @var bool
     */
    protected static $init = false;
    /**
     * @var int
     */
    protected $show_input_label = self::SHOW_INPUT_LABEL_AUTO;
    /**
     * @var TabsInputGUITab[]
     */
    protected $tabs = [];
    /**
     * @var array
     */
    protected $value = [];


    /**
     * TabsInputGUI constructor
     *
     * @param string $title
     * @param string $post_var
     */
    public function __construct(string $title = "", string $post_var = "")
    {
        parent::__construct($title, $post_var);

        self::init(); // TODO: Pass $plugin
    }


    /**
     * @param PluginInterface|null $plugin
     */
    public static function init(/*?*/ PluginInterface $plugin = null) : void
    {
        if (self::$init === false) {
            self::$init = true;

            $version_parameter = PluginVersionParameter::getInstance();
            if ($plugin !== null) {
                $version_parameter = $version_parameter->withPlugin($plugin);
            }

            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

            self::dic()->ui()->mainTemplate()->addCss($version_parameter->appendToUrl($dir . "/css/tabs_input_gui.css"));
        }
    }


    /**
     *
     */
    public function __clone()
    {
        $this->tabs = array_map(function (TabsInputGUITab $tab) : TabsInputGUITab {
            return clone $tab;
        }, $this->tabs);
    }


    /**
     * @param TabsInputGUITab $tab
     */
    public function addTab(TabsInputGUITab $tab) : void
    {
        $this->tabs[] = $tab;
    }


    /**
     * @inheritDoc
     */
    public function checkInput() : bool
    {
        $ok = true;

        foreach ($this->tabs as $tab) {
            foreach ($tab->getInputs($this->getPostVar(), $this->getValue()) as $org_post_var => $input) {
                $b_value = $_POST[$input->getPostVar()];

                $_POST[$input->getPostVar()] = $_POST[$this->getPostVar()][$tab->getPostVar()][$org_post_var];

                /*if ($this->getRequired()) {
                   $input->setRequired(true);
               }*/

                if (!$input->checkInput()) {
                    $ok = false;
                }

                $_POST[$input->getPostVar()] = $b_value;
            }
        }

        if ($ok) {
            return true;
        } else {
            //$this->setAlert(self::dic()->language()->txt("form_input_not_valid"));

            return false;
        }
    }


    /**
     * @return int
     */
    public function getShowInputLabel() : int
    {
        return $this->show_input_label;
    }


    /**
     * @param int $show_input_label
     */
    public function setShowInputLabel(int $show_input_label) : void
    {
        $this->show_input_label = $show_input_label;
    }


    /**
     * @inheritDoc
     */
    public function getTableFilterHTML() : string
    {
        return $this->render();
    }


    /**
     * @return TabsInputGUITab[]
     */
    public function getTabs() : array
    {
        return $this->tabs;
    }


    /**
     * @param TabsInputGUITab[] $tabs
     */
    public function setTabs(array $tabs) : void
    {
        $this->tabs = $tabs;
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
     * @param array $value
     */
    public function setValue(/*array*/ $value) : void
    {
        if (is_array($value)) {
            $this->value = $value;
        } else {
            $this->value = [];
        }
    }


    /**
     * @param ilTemplate $tpl
     */
    public function insert(ilTemplate $tpl) : void
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
        $tpl = new Template(__DIR__ . "/templates/tabs_input_gui.html");

        foreach ($this->getTabs() as $tab) {
            $inputs = $tab->getInputs($this->getPostVar(), $this->getValue());

            $tpl->setCurrentBlock("tab");

            $post_var = str_replace(["[", "]"], "__", $this->getPostVar() . "_" . $tab->getPostVar());
            $tab_id = "tabsinputgui_tab_" . $post_var;
            $tab_content_id = "tabsinputgui_tab_content_" . $post_var;

            $tpl->setVariableEscaped("TAB_ID", $tab_id);
            $tpl->setVariableEscaped("TAB_CONTENT_ID", $tab_content_id);

            $tpl->setVariableEscaped("TITLE", $tab->getTitle());

            if ($tab->isActive()) {
                $tpl->setVariableEscaped("ACTIVE", " active");
            }

            $tpl->parseCurrentBlock();

            $tpl->setCurrentBlock("tab_content");

            if ($this->getShowInputLabel() === self::SHOW_INPUT_LABEL_AUTO) {
                $tpl->setVariableEscaped("SHOW_INPUT_LABEL", (count($inputs) > 1 ? self::SHOW_INPUT_LABEL_ALWAYS : self::SHOW_INPUT_LABEL_NONE));
            } else {
                $tpl->setVariableEscaped("SHOW_INPUT_LABEL", $this->getShowInputLabel());
            }

            if ($tab->isActive()) {
                $tpl->setVariableEscaped("ACTIVE", " active");
            }

            $tpl->setVariableEscaped("TAB_ID", $tab_id);
            $tpl->setVariableEscaped("TAB_CONTENT_ID", $tab_content_id);

            if (!empty($tab->getInfo())) {
                $info_tpl = new Template(__DIR__ . "/../PropertyFormGUI/Items/templates/input_gui_input_info.html");

                $info_tpl->setVariableEscaped("INFO", $tab->getInfo());

                $tpl->setVariable("INFO", self::output()->getHTML($info_tpl));
            }

            $tpl->setVariable("INPUTS", Items::renderInputs($inputs));

            $tpl->parseCurrentBlock();
        }

        return self::output()->getHTML($tpl);
    }


    /**
     * @param array $values
     */
    public function setValueByArray(/*array*/ $values) : void
    {
        $this->setValue($values[$this->getPostVar()]);
    }
}
