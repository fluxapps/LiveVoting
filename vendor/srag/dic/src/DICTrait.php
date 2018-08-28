<?php

namespace srag\DIC;

use ilPlugin;
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
	 * @ throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
	 */
	protected static function dic() {
		self::checkPluginClassNameConst();

		return DICCache::dic();
	}


	/**
	 * Get ilPlugin instance
	 *
	 * @return ilPlugin ilPlugin instance of your plugin
	 * @ throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
	 */
	protected static function pl() {
		self::checkPluginClassNameConst();

		return DICCache::pl(static::PLUGIN_CLASS_NAME);
	}


	/**
	 * Get plugin directory
	 *
	 * @return string Plugin directory
	 * @ throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
	 */
	protected static function directory() {
		return self::pl()->getDirectory();
	}


	/**
	 * Output html
	 *
	 * @param string|ilTemplate $html HTML code or ilTemplate instance
	 * @param bool              $main Display main skin?
	 */
	protected static function output($html, $main = true) {
		if ($html instanceof ilTemplate) {
			$html = $html->get();
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
	 * @param string $template                 Template path
	 * @param bool   $remove_unknown_variables Should remove unknown variables?
	 * @param bool   $remove_empty_blocks      Should remove empty blocks?
	 * @param bool   $plugin                   Plugin template or ILIAS core template?
	 *
	 * @return ilTemplate ilTemplate instance
	 * @ throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
	 */
	protected static function template($template, $remove_unknown_variables = true, $remove_empty_blocks = true, $plugin = true) {
		if ($plugin) {
			return self::pl()->getTemplate($template, $remove_unknown_variables, $remove_empty_blocks);
		} else {
			return new ilTemplate($template, $remove_unknown_variables, $remove_empty_blocks);
		}
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
	 * @ throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
	 */
	protected static function translate($key, $module = "", array $placeholders = [], $plugin = true, $lang = "", $default = "MISSING %s") {
		if (!empty($module)) {
			$key = $module . "_" . $key;
		}

		if ($plugin) {
			if (empty($lang)) {
				$txt = self::pl()->txt($key);
			} else {
				$lng = DICCache::language($lang);

				$lng->loadLanguageModule(self::pl()->getPrefix());

				$txt = $lng->txt(self::pl()->getPrefix() . "_" . $key, self::pl()->getPrefix());
			}
		} else {
			if (empty($lang)) {
				$txt = self::dic()->language()->txt($key);
			} else {
				$lng = DICCache::language($lang);

				if (!empty($module)) {
					$lng->loadLanguageModule($module);
				}

				$txt = $lng->txt($key);
			}
		}

		if (!(empty($txt) || ($txt[0] === "-" && $txt[strlen($txt) - 1] === "-") || $txt === "MISSING" || strpos($txt, "MISSING ") === 0)) {
			$txt = vsprintf($txt, $placeholders);
		} else {
			if ($default !== NULL) {
				$txt = sprintf($default, $key);
			}
		}

		return $txt;
	}


	/**
	 * @throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
	 */
	private static function checkPluginClassNameConst() {
		if (!defined("static::PLUGIN_CLASS_NAME") || empty(static::PLUGIN_CLASS_NAME)) {
			throw new DICException("Your class needs to implement the PLUGIN_CLASS_NAME constant!");
		}
	}
}
