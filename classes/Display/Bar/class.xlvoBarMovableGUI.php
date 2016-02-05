<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarGUI.php');

/**
 * Class xlvoMovableBarGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoBarMovableGUI implements xlvoBarGUI {

	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var xlvoOption[]
	 */
	protected $options = array();


	/**
	 * xlvoBarMovableGUI constructor.
	 * @param xlvoOption[] $options
	 */
	public function __construct(array $options) {
		$this->options = $options;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Display/tpl.bar_movable.html', false, true);
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$i = 1;
		foreach ($this->options as $xlvoOption) {
			$this->tpl->setCurrentBlock('option');
			$this->tpl->setVariable('ID', $xlvoOption->getId());
			$this->tpl->setVariable('OPTION_LETTER', chr(64 + $i));
			$this->tpl->setVariable('OPTION', $xlvoOption->getText());
			$this->tpl->parseCurrentBlock();
			$i ++;
		}

		return $this->tpl->get();
	}
}
