<?php

namespace srag\DIC;

use ilConfirmationGUI;
use ilPlugin;
use ilPropertyFormGUI;
use ilTable2GUI;
use ilTemplate;

/**
 * Trait DICTrait
 *
 * @package srag\DIC
 */
trait DICTrait {

	/* *
	 * @var string
	 *
	 * @abstract
	 *
	 * TODO: Implement Constants in Traits in PHP Core
	 * /
	const PLUGIN_CLASS_NAME = "";*/

	/**
	 * Get DIC interface
	 *
	 * @return DICInterface DIC interface
	 */
	protected static final function dic() {
		return DICStatic::dic();
	}


	/**
	 * Get ilPlugin instance
	 *
	 * @return ilPlugin ilPlugin instance of your plugin
	 *
	 * @throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
	 * @throws DICException Class $plugin_class_name not exists!
	 * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
	 */
	protected static final function pl() {
		self::checkPluginClassNameConst();

		return DICStatic::pl(static::PLUGIN_CLASS_NAME);
	}


	/**
	 * Get plugin directory
	 *
	 * @return string Plugin directory
	 *
	 * @throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
	 * @throws DICException Class $plugin_class_name not exists!
	 * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
	 */
	protected static final function directory() {
		self::checkPluginClassNameConst();

		return DICStatic::directory(static::PLUGIN_CLASS_NAME);
	}


	/**
	 * Output html
	 *
	 * @param string|ilTemplate|ilConfirmationGUI|ilPropertyFormGUI|ilTable2GUI $html HTML code or some gui instance
	 * @param bool                                                              $main Display main skin?
	 */
	protected static final function output($html, $main = true) {
		DICStatic::output($html, $main);
	}


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
	 * @throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant
	 * @throws DICException Class $plugin_class_name not exists!
	 * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
	 */
	protected static final function template($template, $remove_unknown_variables = true, $remove_empty_blocks = true, $plugin = true) {
		self::checkPluginClassNameConst();

		return DICStatic::template(static::PLUGIN_CLASS_NAME, $template, $remove_unknown_variables, $remove_empty_blocks, $plugin);
	}


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
	 * @throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
	 * @throws DICException Class $plugin_class_name not exists!
	 * @throws DICException Please use the placeholders feature and not direct `sprintf` or `vsprintf` in your code!
	 * @throws DICException Please use only one placeholder in the default text for the key!
	 * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
	 */
	protected static final function translate($key, $module = "", array $placeholders = [], $plugin = true, $lang = "", $default = "MISSING %s") {
		self::checkPluginClassNameConst();

		return DICStatic::translate(static::PLUGIN_CLASS_NAME, $key, $module, $placeholders, $plugin, $lang = "", $default);
	}


	/**
	 * @throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
	 */
	private static final function checkPluginClassNameConst() {
		if (!defined("static::PLUGIN_CLASS_NAME") || empty(static::PLUGIN_CLASS_NAME)) {
			throw new DICException("Your class needs to implement the PLUGIN_CLASS_NAME constant!");
		}
	}
}
