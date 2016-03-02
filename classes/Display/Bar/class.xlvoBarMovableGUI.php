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
	 * @var array
	 */
	protected $order = array();
	/**
	 * @var int
	 */
	protected $vote_id = null;


	/**
	 * xlvoBarMovableGUI constructor.
	 *
	 * @param array $options
	 * @param array $order
	 */
	public function __construct(array $options, array $order = array(), $vote_id = null) {
		$this->options = $options;
		$this->order = $order;
		$this->vote_id = $vote_id;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Display/Bar/tpl.bar_movable.html', false, true);
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$i = 1;
		$this->tpl->setVariable('VOTE_ID', $this->vote_id);
		if (count($this->order) > 0) {
			$this->tpl->setVariable('YOUR_ORDER', ilLiveVotingPlugin::getInstance()->txt('qtype_4_your_order'));
			foreach ($this->order as $value) {
				$xlvoOption = $this->options[$value];
				if (!$xlvoOption instanceof xlvoOption) {
					continue;
				}
				$this->tpl->setCurrentBlock('option');
				$this->tpl->setVariable('ID', $xlvoOption->getId());
				//				$this->tpl->setVariable('OPTION_LETTER', chr(64 + $i));
				$this->tpl->setVariable('OPTION', $xlvoOption->getText());
				$this->tpl->parseCurrentBlock();
				$i ++;
			}
		} else {
			foreach ($this->options as $xlvoOption) {
				$this->tpl->setCurrentBlock('option');
				$this->tpl->setVariable('ID', $xlvoOption->getId());
				//				$this->tpl->setVariable('OPTION_LETTER', chr(64 + $i));
				$this->tpl->setVariable('OPTION', $xlvoOption->getText());
				$this->tpl->parseCurrentBlock();
				$i ++;
			}
		}

		return $this->tpl->get();
	}
}
