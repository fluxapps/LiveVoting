<?php

namespace srag\LibrariesNamespaceChanger;

use Composer\Script\Event;

/**
 * Class LibrariesNamespaceChanger
 *
 * @package srag\LibrariesNamespaceChanger
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @access  package
 */
final class LibrariesNamespaceChanger {

	/**
	 * @var self
	 */
	private static $instance = NULL;
	/**
	 * @var array
	 */
	private static $libraries = [
		"ActiveRecordConfig" => "ActiveRecordConfig",
		"BexioCurl" => "BexioCurl",
		"CustomInputGUIs" => "CustomInputGUIs",
		"DIC" => "DIC",
		"JasperReport" => "JasperReport",
		"JiraCurl" => "JiraCurl",
		"RemovePluginDataConfirm" => "RemovePluginDataConfirm"
	];
	/**
	 * @var array
	 */
	private static $exts = [
		"json",
		"md",
		"php"
	];
	/**
	 * @var string
	 *
	 * @access package
	 */
	const PLUGIN_NAME_REG_EXP = "/\/([A-Za-z0-9_]+)\/vendor\//";
	/**
	 * @var string
	 *
	 * @access package
	 */
	const SRAG = "srag";


	/**
	 * @param Event $event
	 *
	 * @return self
	 */
	private static function getInstance(Event $event)/*: self*/ {
		if (self::$instance === NULL) {
			self::$instance = new self($event);
		}

		return self::$instance;
	}


	/**
	 * @param Event $event
	 *
	 * @access package
	 */
	public static function rewriteLibrariesNamespaces(Event $event) {
		self::getInstance($event)->doRewriteLibrariesNamespaces();
	}


	/**
	 * @var Event
	 */
	private $event;


	/**
	 * LibrariesNamespaceChanger constructor
	 *
	 * @param Event $event
	 */
	private function __construct(Event $event) {
		$this->event = $event;
	}


	/**
	 *
	 */
	private function doRewriteLibrariesNamespaces()/*: void*/ {
		$plugin_name = $this->getPluginName();

		if (!empty($plugin_name)) {

			$libraries = array_map(function (/*string*/
				$library)/*: string*/ {
				return __DIR__ . "/../../" . strtolower($library);
			}, self::$libraries);

			$files = [];
			foreach ($libraries as $library => $folder) {
				if (is_dir($folder)) {
					$this->getFiles($folder, $files);
				}
			}
			$this->getFiles(__DIR__ . "/../../../composer", $files);

			foreach ($libraries as $library => $folder) {
				if (is_dir($folder)) {

					foreach ($files as $file) {
						$code = file_get_contents($file);

						$replaces = [
							self::SRAG . "\\" . $library => self::SRAG . "\\" . $library . "\\" . $plugin_name,
							self::SRAG . "\\\\" . $library => self::SRAG . "\\\\" . $library . "\\\\" . $plugin_name
						];

						foreach ($replaces as $search => $replace) {
							if (strpos($code, $replace) === false) {
								$code = str_replace($search, $replace, $code);
							}
						}

						file_put_contents($file, $code);
					}
				}
			}
		}
	}


	/**
	 * @return string
	 */
	private function getPluginName()/*: string*/ {
		$matches = [];
		preg_match(self::PLUGIN_NAME_REG_EXP, __DIR__, $matches);

		if (is_array($matches) && count($matches) >= 2) {
			$plugin_name = $matches[1];

			return $plugin_name;
		} else {
			return "";
		}
	}


	/**
	 * @param string $folder
	 * @param array  $files
	 */
	private function getFiles(/*string*/
		$folder, array &$files = [])/*: void*/ {
		$paths = scandir($folder);

		foreach ($paths as $file) {
			if ($file !== "." && $file !== "..") {
				$path = $folder . "/" . $file;

				if (is_dir($path)) {
					$this->getFiles($path, $files);
				} else {
					$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
					if (in_array($ext, self::$exts)) {
						array_push($files, $path);
					}
				}
			}
		}
	}
}
