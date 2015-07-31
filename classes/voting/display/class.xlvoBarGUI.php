<?php

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


	protected function render() {
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$this->render();

		return $this->tpl->get();
	}
}