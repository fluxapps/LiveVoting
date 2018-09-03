<?php

namespace srag\DIC\Plugin;

use ilConfirmationGUI;
use ilPlugin;
use ilPropertyFormGUI;
use ilTable2GUI;
use ilTemplate;
use ilTemplateException;
use srag\DIC\Exception\DICException;

/**
 * Interface PluginInterface
 *
 * @package srag\DIC\Plugin
 */
interface PluginInterface {

	/**
	 * Get plugin directory
	 *
	 * @return string Plugin directory
	 */
	public function directory();


	/**
	 * Output html
	 *
	 * @param string|ilTemplate|ilConfirmationGUI|ilPropertyFormGUI|ilTable2GUI $html HTML code or some gui instance
	 * @param bool                                                              $main Display main skin?
	 *
	 * @throws ilTemplateException
	 */
	public function output($html, $main = true);


	/**
	 * Get a template
	 *
	 * @param string $template                 Template path
	 * @param bool   $remove_unknown_variables Should remove unknown variables?
	 * @param bool   $remove_empty_blocks      Should remove empty blocks?
	 * @param bool   $plugin                   Plugin template or ILIAS core template?
	 *
	 * @return ilTemplate ilTemplate instance
	 *
	 * @throws ilTemplateException
	 */
	public function template($template, $remove_unknown_variables = true, $remove_empty_blocks = true, $plugin = true);


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
	public function translate($key, $module = "", array $placeholders = [], $plugin = true, $lang = "", $default = "MISSING %s");


	/**
	 * Get ILIAS plugin object instance
	 *
	 * @return ilPlugin ILIAS plugin object instance
	 */
	public function getPluginObject();
}
