<?php

namespace srag\CustomInputGUIs\LiveVoting\MultiSelectSearchInputGUI;

use ilMultiSelectInputGUI;
use ilTableFilterItem;
use ilTemplate;
use ilToolbarItem;
use ilUtil;
use srag\CustomInputGUIs\LiveVoting\Template\Template;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class MultiSelectSearchInputGUI
 *
 * @package    srag\CustomInputGUIs\LiveVoting\MultiSelectSearchInputGUI
 *
 * @deprecated Please switch to `MultiSelectSearchNewInputGUI`
 */
class MultiSelectSearchInputGUI extends ilMultiSelectInputGUI implements ilTableFilterItem, ilToolbarItem
{

    use DICTrait;

    /**
     * @var string
     *
     * @deprecated
     */
    protected $ajax_link;
    /**
     * @var string
     *
     * @deprecated
     */
    protected $css_class;
    /**
     * @var string
     *
     * @deprecated
     */
    protected $height;
    /**
     * @var ilTemplate
     *
     * @deprecated
     */
    protected $input_template;
    /**
     * @var int|null
     *
     * @deprecated
     */
    protected $minimum_input_length = null;
    /**
     * @var string
     *
     * @deprecated
     */
    protected $width;


    /**
     * MultiSelectSearchInputGUI constructor
     *
     * @param string $title
     * @param string $post_var
     *
     * @deprecated
     */
    public function __construct(string $title = "", string $post_var = "")
    {
        if (substr($post_var, -2) != "[]") {
            $post_var = $post_var . "[]";
        }
        parent::__construct($title, $post_var);

        $dir = __DIR__;
        $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

        self::dic()->ui()->mainTemplate()->addJavaScript($dir . "/../../node_modules/select2/dist/js/select2.full.min.js");
        self::dic()->ui()->mainTemplate()->addJavaScript($dir . "/../../node_modules/select2/dist/js/i18n/" . self::dic()->user()->getCurrentLanguage()
            . ".js");
        self::dic()->ui()->mainTemplate()->addCss($dir . "/../../node_modules/select2/dist/css/select2.min.css");
        self::dic()->ui()->mainTemplate()->addCss($dir . "/css/multiselectsearchinputgui.css");
        $this->setInputTemplate(new Template(__DIR__ . "/templates/tpl.multiple_select.html", true, true));
        $this->setWidth("308px");
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function checkInput() : bool
    {
        if ($this->getRequired() && empty($this->getValue())) {
            $this->setAlert(self::dic()->language()->txt("msg_input_is_required"));

            return false;
        }

        return true;
    }


    /**
     * @return string
     *
     * @deprecated
     */
    public function getAjaxLink() : string
    {
        return $this->ajax_link;
    }


    /**
     * @param string $ajax_link setting the ajax link will lead to ignoration of the "setOptions" function as the link given will be used to get the
     *
     * @deprecated
     */
    public function setAjaxLink(string $ajax_link)/*: void*/
    {
        $this->ajax_link = $ajax_link;
    }


    /**
     * @return string
     *
     * @deprecated
     */
    public function getCssClass() : string
    {
        return $this->css_class;
    }


    /**
     * @param string $css_class
     *
     * @deprecated
     */
    public function setCssClass(string $css_class)/*: void*/
    {
        $this->css_class = $css_class;
    }


    /**
     * @return string
     *
     * @deprecated setting inline style items from the controller is bad practice. please use the setClass together with an appropriate css class.
     *
     * @deprecated
     */
    public function getHeight() : string
    {
        return $this->height;
    }


    /**
     * @param string $height
     *
     * @deprecated setting inline style items from the controller is bad practice. please use the setClass together with an appropriate css class.
     *
     * @deprecated
     */
    public function setHeight(/*string*/ $height)/*: void*/
    {
        $this->height = $height;
    }


    /**
     * @return ilTemplate
     *
     * @deprecated
     */
    public function getInputTemplate() : ilTemplate
    {
        return $this->input_template;
    }


    /**
     * @param ilTemplate $input_template
     *
     * @deprecated
     */
    public function setInputTemplate(ilTemplate $input_template)/*: void*/
    {
        $this->input_template = $input_template;
    }


    /**
     * @return int
     *
     * @deprecated
     */
    public function getMinimumInputLength() : int
    {
        if ($this->minimum_input_length !== null) {
            return $this->minimum_input_length;
        } else {
            return (!empty($this->getAjaxLink()) ? 1 : 0);
        }
    }


    /**
     * @param int|null $minimum_input_length
     *
     * @deprecated
     */
    public function setMinimumInputLength(/*?*/ int $minimum_input_length = null)/*: void*/
    {
        $this->minimum_input_length = $minimum_input_length;
    }


    /**
     * @return array
     *
     * @deprecated
     */
    public function getSubItems() : array
    {
        return array();
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function getTableFilterHTML() : string
    {
        return $this->render();
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function getToolbarHTML() : string
    {
        return $this->render();
    }


    /**
     * @return string
     *
     * @deprecated setting inline style items from the controller is bad practice. please use the setClass together with an appropriate css class.
     *
     * @deprecated
     */
    public function getWidth() : string
    {
        return $this->width;
    }


    /**
     * @param string $width
     *
     * @deprecated setting inline style items from the controller is bad practice. please use the setClass together with an appropriate css class.
     *
     * @deprecated
     */
    public function setWidth(/*string*/ $width)/*: void*/
    {
        $this->width = $width;
    }


    /**
     * @return string
     *
     * @deprecated
     */
    public function render() : string
    {
        $tpl = $this->getInputTemplate();
        $values = $this->getValue();
        $options = $this->getOptions();

        $postvar = $this->getPostVar();
        /*if(substr($postvar, -3) == "[]]")
        {
            $postvar = substr($postvar, 0, -3)."]";
        }*/

        $tpl->setVariable("POST_VAR", $postvar);

        //Multiselect Bugfix
        //$id = substr($this->getPostVar(), 0, -2);
        $tpl->setVariable("ID", $this->getFieldId());
        //$tpl->setVariable("ID", $this->getPostVar());

        $tpl->setVariable("WIDTH", $this->getWidth());
        $tpl->setVariable("HEIGHT", $this->getHeight());
        $tpl->setVariable("PLACEHOLDER", "");
        $tpl->setVariable("MINIMUM_INPUT_LENGTH", $this->getMinimumInputLength());
        $tpl->setVariable("Class", $this->getCssClass());

        if (!empty($this->getAjaxLink())) {
            $tpl->setVariable("AJAX_LINK", $this->getAjaxLink());
        }

        if ($this->getDisabled()) {
            $tpl->setVariable("ALL_DISABLED", "disabled=\"disabled\"");
        }

        if ($options) {
            foreach ($options as $option_value => $option_text) {
                $selected = in_array($option_value, $values);

                if (!empty($this->getAjaxLink()) && !$selected) {
                    continue;
                }

                $tpl->setCurrentBlock("item");
                if ($this->getDisabled()) {
                    $tpl->setVariable("DISABLED", " disabled=\"disabled\"");
                }
                if ($selected) {
                    $tpl->setVariable("SELECTED", "selected");
                }

                $tpl->setVariable("VAL", ilUtil::prepareFormOutput($option_value));
                $tpl->setVariable("TEXT", $option_text);
                $tpl->parseCurrentBlock();
            }
        }

        return self::output()->getHTML($tpl);
    }


    /**
     * @param string $a_postvar
     *
     * @deprecated
     */
    public function setPostVar(/*string*/ $a_postvar)/*: void*/
    {
        if (substr($a_postvar, -2) != "[]") {
            $a_postvar = $a_postvar . "[]";
        }
        parent::setPostVar($a_postvar);
    }


    /**
     * @param array $values
     *
     * @deprecated
     */
    public function setValueByArray(/*array*/ $values)/*: void*/
    {
        //		print_r($array);

        $val = $values[$this->searchPostVar()];
        if (is_array($val)) {
            $val;
        } elseif (!$val) {
            $val = array();
        } else {
            $val = explode(",", $val);
        }
        $this->setValue($val);
    }


    /**
     * This implementation might sound silly. But the multiple select input used parses the post vars differently if you use ajax. thus we have to do this stupid "trick". Shame on select2 project ;)
     *
     * @return string the real postvar.
     *
     * @deprecated
     */
    protected function searchPostVar() : string
    {
        if (substr($this->getPostVar(), -2) == "[]") {
            return substr($this->getPostVar(), 0, -2);
        } else {
            return $this->getPostVar();
        }
    }
}
