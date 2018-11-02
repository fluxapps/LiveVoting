<?php

namespace srag\CustomInputGUIs\Template;

use ilTemplate;

/**
 * Class Template
 *
 * @package srag\CustomInputGUIs\Template
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Template extends ilTemplate {

	/**
	 * @param bool $a_force
	 */
	public function fillJavaScriptFiles($a_force = false) {
		parent::fillJavaScriptFiles($a_force);

		if ($this->blockExists("js_file")) {
			reset($this->js_files);

			foreach ($this->js_files as $file) {
				if (strpos($file, "data:application/javascript;base64,") === 0) {
					$this->fillJavascriptFile($file, "");
				}
			}
		}
	}
}
