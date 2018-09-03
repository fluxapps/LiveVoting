<?php

namespace srag\DIC\Plugin;

use Exception;
use ilConfirmationGUI;
use ilLanguage;
use ilPlugin;
use ilPropertyFormGUI;
use ilTable2GUI;
use ilTemplate;
use srag\DIC\DICTrait;
use srag\DIC\Exception\DICException;

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
	private $plugin;


	/**
	 * Plugin constructor
	 *
	 * @param ilPlugin $plugin
	 */
	public function __construct(ilPlugin $plugin) {
		$this->plugin = $plugin;
	}


	/**
	 * @inheritdoc
	 */
	public function directory() {
		return $this->plugin->getDirectory();
	}


	/**
	 * @inheritdoc
	 */
	public function output($html, $main = true) {
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
	 * @inheritdoc
	 */
	public function template($template, $remove_unknown_variables = true, $remove_empty_blocks = true, $plugin = true) {
		if ($plugin) {
			return $this->plugin->getTemplate($template, $remove_unknown_variables, $remove_empty_blocks);
		} else {
			return new ilTemplate($template, $remove_unknown_variables, $remove_empty_blocks);
		}
	}


	/**
	 * @inheritdoc
	 */
	public function translate($key, $module = "", array $placeholders = [], $plugin = true, $lang = "", $default = "MISSING %s") {
		if (!empty($module)) {
			$key = $module . "_" . $key;
		}

		if ($plugin) {
			if (empty($lang)) {
				$txt = $this->plugin->txt($key);
			} else {
				$lng = self::language($lang);

				$lng->loadLanguageModule($this->plugin->getPrefix());

				$txt = $lng->txt($this->plugin->getPrefix() . "_" . $key, $this->plugin->getPrefix());
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
	 * @inheritdoc
	 */
	public function getPluginObject() {
		return $this->plugin;
	}


	/**
	 * @param string $lang
	 *
	 * @return ilLanguage
	 */
	protected static function language($lang) {
		if (!isset(self::$languages[$lang])) {
			self::$languages[$lang] = new ilLanguage($lang);
		}

		return self::$languages[$lang];
	}
}
