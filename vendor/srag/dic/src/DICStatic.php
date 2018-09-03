<?php

namespace srag\DIC;

use Exception;
use ilConfirmationGUI;
use ilLanguage;
use ilLogLevel;
use ilPlugin;
use ilPropertyFormGUI;
use ilTable2GUI;
use ilTemplate;

/**
 * Class DICStatic
 *
 * @package srag\DIC
 */
final class DICStatic {

	/**
	 * @var DICInterface|null
	 */
	private static $dic = NULL;
	/**
	 * @var ilLanguage[]
	 */
	private static $languages = [];
	/**
	 * @var ilPlugin[]
	 */
	private static $pl = [];


	/**
	 * Get DIC interface
	 *
	 * @return DICInterface DIC interface
	 */
	public static final function dic() {
		if (self::$dic === NULL) {
			if (ILIAS_VERSION_NUMERIC >= "5.2") {
				global $DIC;
				self::$dic = new NewDIC($DIC);
			} else {
				global $GLOBALS;
				self::$dic = new LegacyDIC($GLOBALS);
			}
		}

		return self::$dic;
	}


	/**
	 * @param string $plugin_class_name
	 *
	 * @return ilPlugin
	 *
	 * @throws DICException Class $plugin_class_name not exists!
	 * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
	 */
	public static final function pl($plugin_class_name) {
		if (!isset(self::$pl[$plugin_class_name])) {
			if (!class_exists($plugin_class_name)) {
				throw new DICException("Class $plugin_class_name not exists!");
			}

			if (method_exists($plugin_class_name, "getInstance")) {
				self::$pl[$plugin_class_name] = $plugin_class_name::getInstance();
			} else {
				self::dic()->log()->write("DICLog: Please implement $plugin_class_name::getInstance()!", ilLogLevel::DEBUG);
				self::$pl[$plugin_class_name] = new $plugin_class_name();
			}
		}

		return self::$pl[$plugin_class_name];
	}


	/**
	 * Get plugin directory
	 *
	 * @param string $plugin_class_name
	 *
	 * @return string Plugin directory
	 *
	 * @throws DICException Class $plugin_class_name not exists!
	 * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
	 */
	public static final function directory($plugin_class_name) {
		return self::pl($plugin_class_name)->getDirectory();
	}


	/**
	 * Output html
	 *
	 * @param string|ilTemplate|ilConfirmationGUI|ilPropertyFormGUI|ilTable2GUI $html HTML code or some gui instance
	 * @param bool                                                              $main Display main skin?
	 */
	public static final function output($html, $main = true) {
		switch (true) {
			case ($html instanceof ilTemplate):
				$html = $html->get();
				break;
			case ($html instanceof ilConfirmationGUI):
			case ($html instanceof ilPropertyFormGUI):
			case ($html instanceof ilTable2GUI):
				$html = $html->getHTML();
				break;
			default:
				$html = strval($html);
				break;
		}

		if (self::dic()->ctrl()->isAsynch()) {
			echo $html;
		} else {
			if ($main) {
				self::dic()->template()->getStandardTemplate();
			}
			self::dic()->template()->setContent($html);
			self::dic()->template()->show();
		}

		exit;
	}


	/**
	 * Get a template
	 *
	 * @param string $plugin_class_name
	 * @param string $template                 Template path
	 * @param bool   $remove_unknown_variables Should remove unknown variables?
	 * @param bool   $remove_empty_blocks      Should remove empty blocks?
	 * @param bool   $plugin                   Plugin template or ILIAS core template?
	 *
	 * @return ilTemplate ilTemplate instance
	 *
	 * @throws DICException Class $plugin_class_name not exists!
	 * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
	 */
	public static final function template($plugin_class_name, $template, $remove_unknown_variables = true, $remove_empty_blocks = true, $plugin = true) {
		if ($plugin) {
			return self::pl($plugin_class_name)->getTemplate($template, $remove_unknown_variables, $remove_empty_blocks);
		} else {
			return new ilTemplate($template, $remove_unknown_variables, $remove_empty_blocks);
		}
	}


	/**
	 * Translate text
	 *
	 * @param string $plugin_class_name
	 * @param string $key          Language key
	 * @param string $module       Language module
	 * @param array  $placeholders Placeholders in your language texst to replace with vsprintf
	 * @param bool   $plugin       Plugin language or ILIAS core language?
	 * @param string $lang         Possibly specific language, otherwise current language, if empty
	 * @param string $default      Default text, if language key not exists
	 *
	 * @return string Translated text
	 *
	 * @throws DICException Class $plugin_class_name not exists!
	 * @throws DICException Please use the placeholders feature and not direct `sprintf` or `vsprintf` in your code!
	 * @throws DICException Please use only one placeholder in the default text for the key!
	 * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
	 */
	public static final function translate($plugin_class_name, $key, $module = "", array $placeholders = [], $plugin = true, $lang = "", $default = "MISSING %s") {
		if (!empty($module)) {
			$key = $module . "_" . $key;
		}

		if ($plugin) {
			if (empty($lang)) {
				$txt = self::pl($plugin_class_name)->txt($key);
			} else {
				$lng = self::language($lang);

				$lng->loadLanguageModule(self::pl($plugin_class_name)->getPrefix());

				$txt = $lng->txt(self::pl($plugin_class_name)->getPrefix() . "_" . $key, self::pl($plugin_class_name)->getPrefix());
			}
		} else {
			if (empty($lang)) {
				$txt = self::dic()->language()->txt($key);
			} else {
				$lng = self::language($lang);

				if (!empty($module)) {
					$lng->loadLanguageModule($module);
				}

				$txt = $lng->txt($key);
			}
		}

		if (!(empty($txt) || ($txt[0] === "-" && $txt[strlen($txt) - 1] === "-") || $txt === "MISSING" || strpos($txt, "MISSING ") === 0)) {
			try {
				$txt = vsprintf($txt, $placeholders);
			} catch (Exception $ex) {
				throw new DICException("Please use the placeholders feature and not direct `sprintf` or `vsprintf` in your code!");
			}
		} else {
			if ($default !== NULL) {
				try {
					$txt = sprintf($default, $key);
				} catch (Exception $ex) {
					throw new DICException("Please use only one placeholder in the default text for the key!");
				}
			}
		}

		return $txt;
	}


	/**
	 * @param string $lang
	 *
	 * @return ilLanguage
	 */
	protected static final function language($lang) {
		if (!isset(self::$languages[$lang])) {
			self::$languages[$lang] = new ilLanguage($lang);
		}

		return self::$languages[$lang];
	}


	/**
	 * DICStatic constructor
	 */
	private function __construct() {

	}
}
