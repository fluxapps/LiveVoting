<?php

namespace srag\DIC\Plugin;

use Exception;
use ilConfirmationGUI;
use ilLanguage;
use ilPlugin;
use ilPropertyFormGUI;
use ilTable2GUI;
use ilTemplate;
use JsonSerializable;
use srag\DIC\DICTrait;
use srag\DIC\Exception\DICException;
use stdClass;

/**
 * Class Plugin
 *
 * @package srag\DIC\Plugin
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
	 * @access namespace
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
	public function output($value, /*bool*/
		$main = true)/*: void*/ {
		switch (true) {
			// JSON
			case (is_int($value)):
			case (is_double($value)):
			case (is_bool($value)):
			case (is_array($value)):
			case ($value instanceof stdClass):
			case ($value === NULL):
			case ($value instanceof JsonSerializable):
				$value = json_encode($value);

				header("Content-Type: application/json; charset=utf-8");

				echo $value;

				break;

			default:
				switch (true) {
					// HTML
					case (is_string($value)):
						$html = strval($value);
						break;

					// GUI instance
					case ($value instanceof ilTemplate):
						$html = $value->get();
						break;
					case ($value instanceof ilConfirmationGUI):
					case ($value instanceof ilPropertyFormGUI):
					case ($value instanceof ilTable2GUI):
						$html = $value->getHTML();
						break;

					// Not supported!
					default:
						throw new DICException("Class " . get_class($value) . " is not supported for output!");
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

				break;
		}

		exit;
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

		if ($plugin) {
			if (empty($lang)) {
				$txt = $this->plugin_object->txt($key);
			} else {
				$lng = self::getLanguage($lang);

				$lng->loadLanguageModule($this->plugin_object->getPrefix());

				$txt = $lng->txt($this->plugin_object->getPrefix() . "_" . $key, $this->plugin_object->getPrefix());
			}
		} else {
			if (empty($lang)) {
				$txt = self::dic()->language()->txt($key);
			} else {
				$lng = self::getLanguage($lang);

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
