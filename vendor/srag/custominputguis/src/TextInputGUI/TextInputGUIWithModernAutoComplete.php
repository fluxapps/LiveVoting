<?php

namespace srag\CustomInputGUIs\LiveVoting\TextInputGUI;

use iljQueryUtil;
use ilUtil;
use srag\CustomInputGUIs\LiveVoting\Template\Template;
use srag\DIC\LiveVoting\Plugin\PluginInterface;
use srag\DIC\LiveVoting\Version\PluginVersionParameter;

/**
 * Class TextInputGUIWithModernAutoComplete
 *
 * @package srag\CustomInputGUIs\LiveVoting\TextInputGUI
 */
class TextInputGUIWithModernAutoComplete extends TextInputGUI
{

    /**
     * @var bool
     */
    protected static $init = false;


    /**
     * TextInputGUIWithModernAutoComplete constructor
     *
     * @param string $a_title
     * @param string $a_postvar
     */
    public function __construct(string $a_title = "", string $a_postvar = "")
    {
        parent::__construct($a_title, $a_postvar);

        self::init(); // TODO: Pass $plugin
    }


    /**
     * @param PluginInterface|null $plugin
     */
    public static function init(/*?*/ PluginInterface $plugin = null)/*: void*/
    {
        if (self::$init === false) {
            self::$init = true;

            $version_parameter = PluginVersionParameter::getInstance();
            if ($plugin !== null) {
                $version_parameter = $version_parameter->withPlugin($plugin);
            }

            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

            iljQueryUtil::initjQuery();
            iljQueryUtil::initjQueryUI();

            self::dic()->ui()->mainTemplate()->addJavaScript($version_parameter->appendToUrl($dir . "/../../node_modules/babel-polyfill/dist/polyfill.min.js"));

            self::dic()->ui()->mainTemplate()->addJavaScript($version_parameter->appendToUrl($dir . "/js/text_input_gui_with_modern_auto_complete.min.js",
                $dir . "/js/text_input_gui_with_modern_auto_complete.js"));

            self::dic()->ui()->mainTemplate()->addOnLoadCode("il.textinput_more_txt = " . json_encode(self::dic()->language()->txt('autocomplete_more')
                    . ";") . ";");
        }
    }


    /**
     * @inheritDoc
     */
    public function render(/*string*/ $a_mode = "") : string
    {
        $tpl = new Template(__DIR__ . "/templates/text_input_gui_with_modern_auto_complete.html", true, true);
        if (strlen($this->getValue())) {
            $tpl->setCurrentBlock("prop_text_propval");
            $tpl->setVariable("PROPERTY_VALUE", ilUtil::prepareFormOutput($this->getValue()));
            $tpl->parseCurrentBlock();
        }
        if (strlen($this->getInlineStyle())) {
            $tpl->setCurrentBlock("stylecss");
            $tpl->setVariable("CSS_STYLE", ilUtil::prepareFormOutput($this->getInlineStyle()));
            $tpl->parseCurrentBlock();
        }
        if (strlen($this->getCssClass())) {
            $tpl->setCurrentBlock("classcss");
            $tpl->setVariable('CLASS_CSS', ilUtil::prepareFormOutput($this->getCssClass()));
            $tpl->parseCurrentBlock();
        }
        if ($this->getSubmitFormOnEnter()) {
            $tpl->touchBlock("submit_form_on_enter");
        }

        switch ($this->getInputType()) {
            case 'password':
                $tpl->setVariable('PROP_INPUT_TYPE', 'password');
                break;
            case 'hidden':
                $tpl->setVariable('PROP_INPUT_TYPE', 'hidden');
                break;
            case 'text':
            default:
                $tpl->setVariable('PROP_INPUT_TYPE', 'text');
        }
        $tpl->setVariable("ID", $this->getFieldId());
        $tpl->setVariable("SIZE", $this->getSize());
        if ($this->getMaxLength() != null) {
            $tpl->setVariable("MAXLENGTH", $this->getMaxLength());
        }
        if (strlen($this->getSuffix())) {
            $tpl->setVariable("INPUT_SUFFIX", $this->getSuffix());
        }

        $postvar = $this->getPostVar();
        if ($this->getMulti() && substr($postvar, -2) != "[]") {
            $postvar .= "[]";
        }

        if ($this->getDisabled()) {
            if ($this->getMulti()) {
                $value = $this->getMultiValues();
                $hidden = "";
                if (is_array($value)) {
                    foreach ($value as $item) {
                        $hidden .= $this->getHiddenTag($postvar, $item);
                    }
                }
            } else {
                $hidden = $this->getHiddenTag($postvar, $this->getValue());
            }
            if ($hidden) {
                $tpl->setVariable("HIDDEN_INPUT", $hidden);
            }
            $tpl->setVariable("DISABLED", " disabled=\"disabled\"");
        } else {
            $tpl->setVariable("POST_VAR", $postvar);
        }

        // use autocomplete feature?
        /*if ($this->getDataSource()) {
            iljQueryUtil::initjQuery();
            iljQueryUtil::initjQueryUI();

            if ($this->getMulti()) {
                $tpl->setCurrentBlock("ac_multi");
                $tpl->setVariable('MURL_AUTOCOMPLETE', $this->getDataSource());
                $tpl->setVariable('ID_AUTOCOMPLETE', $this->getFieldId());
                $tpl->parseCurrentBlock();

                // set to fields that start with autocomplete selector
                $sel_auto = '[id^="' . $this->getFieldId() . '"]';
            } else {
                // use id for autocomplete selector
                $sel_auto = "#" . $this->getFieldId();
            }

            $tpl->setCurrentBlock("autocomplete_bl");
            if (!$this->ajax_datasource_delimiter and !$this->getDataSourceSubmitOnSelection()) {
                $tpl->setVariable('SEL_AUTOCOMPLETE', $sel_auto);
                $tpl->setVariable('URL_AUTOCOMPLETE', $this->getDataSource());
            } elseif ($this->getDataSourceSubmitOnSelection()) {
                $tpl->setVariable('SEL_AUTOCOMPLETE_AUTOSUBMIT', $sel_auto);
                $tpl->setVariable('URL_AUTOCOMPLETE_AUTOSUBMIT_REQ', $this->getDataSource());
                $tpl->setVariable('URL_AUTOCOMPLETE_AUTOSUBMIT_RESP', $this->getDataSourceSubmitUrl());
            } else {
                $tpl->setVariable('AUTOCOMPLETE_DELIMITER', $this->ajax_datasource_delimiter);
                $tpl->setVariable('SEL_AUTOCOMPLETE_DELIMITER', $sel_auto);
                $tpl->setVariable('URL_AUTOCOMPLETE_DELIMITER', $this->getDataSource());
            }
            $tpl->parseCurrentBlock();

            $tpl->setVariable('MORE_TXT', self::dic()->language()->txt('autocomplete_more'));
        }*/
        $tpl->setVariable('URL_AUTOCOMPLETE', $this->getDataSource());

        if ($a_mode == "toolbar") {
            // block-inline hack, see: http://blog.mozilla.com/webdev/2009/02/20/cross-browser-inline-block/
            // -moz-inline-stack for FF2
            // zoom 1; *display:inline for IE6 & 7
            $tpl->setVariable("STYLE_PAR", 'display: -moz-inline-stack; display:inline-block; zoom: 1; *display:inline;');
        } else {
            $tpl->setVariable("STYLE_PAR", '');
        }

        if ($this->isHtmlAutoCompleteDisabled()) {
            $tpl->setVariable("AUTOCOMPLETE", "autocomplete=\"off\"");
        }

        if ($this->getRequired()) {
            $tpl->setVariable("REQUIRED", "required=\"required\"");
        }

        // multi icons
        if ($this->getMulti() && !$a_mode && !$this->getDisabled()) {
            $tpl->touchBlock("inline_in_bl");
            $tpl->setVariable("MULTI_ICONS", $this->getMultiIconsHTML());
        }

        return self::output()->getHTML($tpl);
    }
}
