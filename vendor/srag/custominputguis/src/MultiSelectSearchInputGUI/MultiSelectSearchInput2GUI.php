<?php

namespace srag\CustomInputGUIs\LiveVoting\MultiSelectSearchInputGUI;

use ilUtil;

/**
 * Class MultiSelectSearchInput2GUI
 *
 * @package    srag\CustomInputGUIs\LiveVoting\MultiSelectSearchInputGUI
 *
 * @deprecated Please switch to `MultiSelectSearchNewInputGUI`
 */
class MultiSelectSearchInput2GUI extends MultiSelectSearchInputGUI
{

    /**
     * @var string
     *
     * @deprecated
     */
    protected $placeholder = "";


    /**
     * @return string
     *
     * @deprecated
     */
    public function getContainerType() : string
    {
        return 'crs';
    }


    /**
     * @return string
     *
     * @deprecated
     */
    public function getPlaceholder() : string
    {
        return $this->placeholder;
    }


    /**
     * @param string $placeholder
     *
     * @deprecated
     */
    public function setPlaceholder(string $placeholder)/*: void*/
    {
        $this->placeholder = $placeholder;
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
     * @return array
     *
     * @deprecated
     */
    public function getValue() : array
    {
        $val = parent::getValue();
        if (is_array($val)) {
            return $val;
        } elseif (!$val) {
            return array();
        } else {
            return explode(',', $val);
        }
    }


    /**
     * @return string
     *
     * @deprecated
     */
    public function render() : string
    {
        $tpl = $this->getInputTemplate();
        $json_values = $this->getValueAsJson();
        $values = $this->getValue();
        $options = $this->getOptions();

        $tpl->setVariable('POST_VAR', $this->getPostVar());
        $tpl->setVariable('ID', $this->stripLastStringOccurrence($this->getPostVar(), "[]"));
        $tpl->setVariable('ESCAPED_ID', $this->escapePostVar($this->getPostVar()));
        $tpl->setVariable('WIDTH', $this->getWidth());
        $tpl->setVariable('PRELOAD', $json_values);
        $tpl->setVariable('HEIGHT', $this->getHeight());
        $tpl->setVariable('PLACEHOLDER', $this->getPlaceholder());
        $tpl->setVariable('MINIMUM_INPUT_LENGTH', $this->getMinimumInputLength());
        $tpl->setVariable("LIMIT_COUNT", $this->getLimitCount());
        $tpl->setVariable('CONTAINER_TYPE', $this->getContainerType());
        $tpl->setVariable('Class', $this->getCssClass());

        if (!empty($this->getAjaxLink())) {
            $tpl->setVariable('AJAX_LINK', $this->getAjaxLink());
        }

        if ($this->getDisabled()) {
            $tpl->setVariable('ALL_DISABLED', 'disabled=\'disabled\'');
        }

        if ($options) {
            foreach ($options as $option_value => $option_text) {
                $selected = in_array($option_value, $values);

                if (!empty($this->getAjaxLink()) && !$selected) {
                    continue;
                }

                $tpl->setCurrentBlock('item');
                if ($this->getDisabled()) {
                    $tpl->setVariable('DISABLED', ' disabled=\'disabled\'');
                }
                if ($selected) {
                    $tpl->setVariable('SELECTED', 'selected');
                }

                $tpl->setVariable('VAL', ilUtil::prepareFormOutput($option_value));
                $tpl->setVariable('TEXT', $option_text);
                $tpl->parseCurrentBlock();
            }
        }

        return self::output()->getHTML($tpl);
    }


    /**
     * @param string $postVar
     *
     * @return string
     *
     * @deprecated
     */
    protected function escapePostVar(string $postVar) : string
    {
        $postVar = $this->stripLastStringOccurrence($postVar, "[]");
        $postVar = str_replace("[", '\\\\[', $postVar);
        $postVar = str_replace("]", '\\\\]', $postVar);

        return $postVar;
    }


    /**
     * @return string
     *
     * @deprecated
     */
    protected function getValueAsJson() : string
    {
        return json_encode(array());
    }


    /**
     * @param string $text
     * @param string $string
     *
     * @return string
     *
     * @deprecated
     */
    private function stripLastStringOccurrence(string $text, string $string) : string
    {
        $pos = strrpos($text, $string);
        if ($pos !== false) {
            $text = substr_replace($text, "", $pos, strlen($string));
        }

        return $text;
    }
}
