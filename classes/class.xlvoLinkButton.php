<?php

/**
 * Class xlvoLinkButton
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoLinkButton extends ilLinkButton {

	public function clearClasses() {
		$this->css = array();
	}


	/**
	 * Prepare render
	 */
	protected function prepareRender() {
		$this->addCSSClass('btn');
	}


	/**
	 * @return xlvoLinkButton
	 */
	public static function getInstance() {
		return new self('xlvo_link');
	}
}
