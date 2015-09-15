<?php

/**
 * Class xlvoBarGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoBarGUI {

	/**
	 * @var ilTemplate
	 */
	protected $tpl;


	public function __construct() {
		global $tpl;
		/**
		 * @var $tpl ilTemplate
		 */
		$this->tpl = $tpl;
	}

	/**
	 * @return string
	 */
	public function getHTML() {
		return $this->tpl->get();
	}
}