<?php

use LiveVoting\Display\Bar\xlvoGeneralBarGUI;

/**
 * Class xlvoAbstractBarGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class xlvoAbstractBarGUI implements xlvoGeneralBarGUI {

	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var bool
	 */
	private $strong = false;
	/**
	 * @var bool
	 */
	private $center = false;
	/**
	 * @var bool
	 */
	private $big = false;
	/**
	 * @var bool
	 */
	private $dark = false;
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;


	/**
	 * xlvoAbstractBarGUI constructor.
	 */
	public function __construct() {
		$this->pl = ilLiveVotingPlugin::getInstance();
	}


	/**
	 *
	 */
	protected function initTemplate() {
		global $DIC;
		$this->tpl = $this->pl->getTemplate('default/Display/Bar/tpl.bar_free_input.html');
		$DIC->ui()->mainTemplate()->addCss($this->pl->getDirectory() . "/templates/default/Display/Bar/bar.css");
	}


	/**
	 *
	 */
	protected function render() {
		$this->initTemplate();
		if ($this->isCenter()) {
			$this->tpl->touchBlock('center');
		}
		if ($this->isBig()) {
			$this->tpl->touchBlock('big');
		}
		if ($this->isDark()) {
			$this->tpl->touchBlock('dark');
		}
		if ($this->isStrong()) {
			$this->tpl->touchBlock('strong');
			$this->tpl->touchBlock('strong_end');
		}
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$this->render();

		return $this->tpl->get();
	}


	/**
	 * @return bool
	 */
	public function isStrong() {
		return $this->strong;
	}


	/**
	 * @param bool $strong
	 */
	public function setStrong($strong) {
		$this->strong = $strong;
	}


	/**
	 * @return bool
	 */
	public function isCenter() {
		return $this->center;
	}


	/**
	 * @param bool $center
	 */
	public function setCenter($center) {
		$this->center = $center;
	}


	/**
	 * @return bool
	 */
	public function isBig() {
		return $this->big;
	}


	/**
	 * @param bool $big
	 */
	public function setBig($big) {
		$this->big = $big;
	}


	/**
	 * @return bool
	 */
	public function isDark() {
		return $this->dark;
	}


	/**
	 * @param bool $dark
	 */
	public function setDark($dark) {
		$this->dark = $dark;
	}
}
