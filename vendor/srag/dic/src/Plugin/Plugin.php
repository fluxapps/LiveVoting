<?php

namespace srag\DIC\LiveVoting\Plugin;

use Exception;
use ilLanguage;
use ilPlugin;
use ilTemplate;
use srag\DIC\LiveVoting\DICTrait;
use srag\DIC\LiveVoting\Exception\DICException;

/**
 * Class Plugin
 *
 * @package srag\DIC\LiveVoting\Plugin
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Plugin implements PluginInterface {

	use DICTrait;
	/**
	 * @var ilLanguage[]
	 */
	private static $languages = [];
	/**
	 * @var ilPlugin
	 */
	private $plugin_object;


	/**
	 * Plugin constructor
	 *
	 * @param ilPlugin $plugin_object
	 *
	 * @internal
	 */
	public function __construct(ilPlugin $plugin_object) {
		$this->plugin_object = $plugin_object;
	}


	/**
	 * @inheritdoc
	 */
	public function directory()/*: string*/ {
		return $this->plugin_object->getDirectory();
	}


	/**
	 * @inheritdoc
	 */
	public function template(/*string*/
		$template, /*bool*/
		$remove_unknown_variables = true, /*bool*/
		$remove_empty_blocks = true, /*bool*/
		$plugin = true)/*: ilTemplate*/ {
		if ($plugin) {
			return $this->plugin_object->getTemplate($template, $remove_unknown_variables, $remove_empty_blocks);
		} else {
			return new ilTemplate($template, $remove_unknown_variables, $remove_empty_blocks);
		}
	}


	/**
	 * @inheritdoc
	 */
	public function translate(/*string*/
		$key, /*string*/
		$module = "", array $placeholders = [], /*bool*/
		$plugin = true, /*string*/
		$lang = "", /*string*/
		$default = "MISSING %s")/*: string*/ {
		if (!empty($module)) {
			$key = $module . "_" . $key;
		}

		if (!empty($lang)) {
			$lng = self::getLanguage($lang);
		} else {
			$lng = self::dic()->language();
		}

		if ($plugin) {
			$lng->loadLanguageModule($this->plugin_object->getPrefix());

			if ($lng->exists($this->plugin_object->getPrefix() . "_" . $key)) {
				$txt = $lng->txt($this->plugin_object->getPrefix() . "_" . $key);
			} else {
				$txt = "";
			}
		} else {
			if (!empty($module)) {
				$lng->loadLanguageModule($module);
			}

			if ($lng->exists($key)) {
				$txt = $lng->txt($key);
			} else {
				$txt = "";
			}
		}

		if (!(empty($txt) || $txt === "MISSING" || strpos($txt, "MISSING ") === 0)) {
			try {
				$txt = vsprintf($txt, $placeholders);
			} catch (Exception $ex) {
				throw new DICException("Please use the placeholders feature and not direct `sprintf` or `vsprintf` in your code!", DICException::CODE_MISUSE_TRANSLATE_WITH_SPRINTF);
			}
		} else {
			if ($default !== NULL) {
				try {
					$txt = sprintf($default, $key);
				} catch (Exception $ex) {
					throw new DICException("Please use only one placeholder in the default text for the key!", DICException::CODE_MISUSE_TRANSLATE_WITH_SPRINTF);
				}
			}
		}

		return strval($txt);
	}


	/**
	 * @inheritdoc
	 */
	public function getPluginObject()/*: ilPlugin*/ {
		return $this->plugin_object;
	}


	/**
	 * @param string $lang
	 *
	 * @return ilLanguage
	 */
	private static final function getLanguage(/*string*/
		$lang)/*: ilLanguage*/ {
		if (!isset(self::$languages[$lang])) {
			self::$languages[$lang] = new ilLanguage($lang);
		}

		return self::$languages[$lang];
	}
}
