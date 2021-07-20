<?php

namespace srag\CustomInputGUIs\LiveVoting\TabsInputGUI;

use ilFormPropertyGUI;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\PropertyFormGUI;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class MultilangualTabsInputGUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\TabsInputGUI
 */
class MultilangualTabsInputGUI
{

    use DICTrait;

    /**
     * MultilangualTabsInputGUI constructor
     */
    private function __construct()
    {

    }


    /**
     * @param array $items
     * @param bool  $default_language
     * @param bool  $default_required
     *
     * @return array
     */
    public static function generate(array $items, bool $default_language = false, bool $default_required = true) : array
    {
        foreach (self::getLanguages($default_language) as $lang_key => $lang_title) {
            $tab_items = [];

            foreach ($items as $item_key => $item) {
                $tab_item = $item;

                if ($default_required && $lang_key === "default") {
                    $tab_item[PropertyFormGUI::PROPERTY_REQUIRED] = true;
                }

                $tab_items[$item_key] = $tab_item;
            }

            $tab = [
                PropertyFormGUI::PROPERTY_CLASS    => TabsInputGUITab::class,
                PropertyFormGUI::PROPERTY_SUBITEMS => $tab_items,
                "setTitle"                         => $lang_title,
                "setActive"                        => ($lang_key === ($default_language ? "default" : self::dic()->language()->getLangKey()))
            ];

            $tabs[$lang_key] = $tab;
        }

        return $tabs;
    }


    /**
     * @param TabsInputGUI        $tabs
     * @param ilFormPropertyGUI[] $inputs
     * @param bool                $default_language
     * @param bool                $default_required
     */
    public static function generateLegacy(TabsInputGUI $tabs, array $inputs, bool $default_language = false, bool $default_required = true) : void
    {
        foreach (self::getLanguages($default_language) as $lang_key => $lang_title) {
            $tab = new TabsInputGUITab($lang_title, $lang_key);
            $tab->setActive($lang_key === ($default_language ? "default" : self::dic()->language()->getLangKey()));

            foreach ($inputs as $input) {
                $tab_input = clone $input;

                if ($default_required && $lang_key === "default") {
                    $tab_input->setRequired(true);
                }

                $tab->addInput($tab_input);
            }

            $tabs->addTab($tab);
        }
    }


    /**
     * @param bool $default
     *
     * @return array
     */
    public static function getLanguages(bool $default = false) : array
    {
        $lang_keys = self::dic()->language()->getInstalledLanguages();

        if ($default) {
            array_unshift($lang_keys, "default");
        }

        return array_combine($lang_keys, array_map("strtoupper", $lang_keys));
    }


    /**
     * @param array       $values
     * @param string|null $lang_key
     * @param string|null $sub_key
     * @param bool        $use_default_if_not_set
     *
     * @return mixed
     */
    public static function getValueForLang(array $values,/*?*/ string $lang_key = null, string $sub_key = null, bool $use_default_if_not_set = true)
    {
        if (empty($lang_key)) {
            $lang_key = self::dic()->language()->getLangKey();
        }

        $value = $values[$lang_key];

        if (!empty($sub_key)) {
            if (!is_array($value)) {
                $value = [];
            }

            $value = $value[$sub_key];
        }

        if (!empty($value)) {
            return $value;
        }

        if ($use_default_if_not_set) {
            $value = $values["default"];

            if (!empty($sub_key)) {
                if (!is_array($value)) {
                    $value = [];
                }

                $value = $value[$sub_key];
            }

            return $value;
        } else {
            return $value;
        }
    }


    /**
     * @param array       $values
     * @param mixed       $value
     * @param string      $lang_key
     * @param string|null $sub_key
     */
    public static function setValueForLang(array &$values, $value, string $lang_key, string $sub_key = null) : void
    {
        if (!empty($sub_key)) {
            if (!is_array($values[$lang_key])) {
                $values[$lang_key] = [];
            }
            $values[$lang_key][$sub_key] = $value;
        } else {
            $values[$lang_key] = $value;
        }
    }
}

