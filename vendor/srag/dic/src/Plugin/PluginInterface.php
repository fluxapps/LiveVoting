<?php

namespace srag\DIC\LiveVoting\Plugin;

use ilPlugin;
use ilTemplateException;
use srag\CustomInputGUIs\LiveVoting\Template\Template;
use srag\DIC\LiveVoting\Exception\DICException;

/**
 * Interface PluginInterface
 *
 * @package srag\DIC\LiveVoting\Plugin
 */
interface PluginInterface
{

    /**
     * Get plugin directory
     *
     * @return string Plugin directory
     */
    public function directory() : string;


    /**
     * Get ILIAS plugin object instance
     *
     * Please avoid to use ILIAS plugin object instance and instead use methods in this class!
     *
     * @return ilPlugin ILIAS plugin object instance
     */
    public function getPluginObject() : ilPlugin;


    /**
     *
     */
    public function reloadCtrlStructure()/* : void*/ ;


    /**
     *
     */
    public function reloadDatabase()/* : void*/ ;


    /**
     *
     */
    public function reloadLanguages()/* : void*/ ;


    /**
     *
     */
    public function reloadPluginXml()/* : void*/ ;


    /**
     * Get a template
     *
     * @param string $template                 Template path
     * @param bool   $remove_unknown_variables Should remove unknown variables?
     * @param bool   $remove_empty_blocks      Should remove empty blocks?
     * @param bool   $plugin                   Plugin template or ILIAS core template?
     *
     * @return Template ilTemplate instance
     *
     * @throws ilTemplateException
     */
    public function template(string $template, bool $remove_unknown_variables = true, bool $remove_empty_blocks = true, bool $plugin = true) : Template;


    /**
     * Translate text
     *
     * @param string $key          Language key
     * @param string $module       Language module
     * @param array  $placeholders Placeholders in your language texst to replace with vsprintf
     * @param bool   $plugin       Plugin language or ILIAS core language?
     * @param string $lang         Possibly specific language, otherwise current language, if empty
     * @param string $default      Default text, if language key not exists
     *
     * @return string Translated text
     *
     * @throws DICException Please use the placeholders feature and not direct `sprintf` or `vsprintf` in your code!
     * @throws DICException Please use only one placeholder in the default text for the key!
     */
    public function translate(string $key, string $module = "", array $placeholders = [], bool $plugin = true, string $lang = "", string $default = "MISSING %s") : string;
}
