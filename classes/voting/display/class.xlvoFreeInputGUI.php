<?php

require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');

class xlvoFreeInputGUI extends ilPropertyFormGUI {

	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var xlvoVoting
	 */
	protected $voting;


	public function __construct(xlvoVoting $voting) {
		global $tpl;
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/tpl.free_input.html', false, false);
		$this->voting = $voting;
		$this->voting_manager = new xlvoVotingManager();
	}


	protected function renderForm() {

		$qu = new ilTextAreaInputGUI('...Frage', 'question');
		$qu->setRequired(true);
		$qu->setUseRte(true);
		$qu->usePurifier(true);
		$qu->setRTESupport($this->voting->getId(), "xlvo", "xlvo_question", NULL, false, "3.4.7");

		$form = new ilPropertyFormGUI();
		$form->addItem($qu);
		$form->addCommandButton('', '...Senden');

		return $form->getHTML();
	}

	protected function render() {
		$option = $this->voting_manager->getOptionsForVoting($this->voting->getId())->first();
		$vote = $this->voting_manager->getVotes($this->voting->getId(), $option->getId(), true)->get();

		$this->tpl->setVariable('INPUT');

	}

	/**
	 * @return string
	 */
	public function getHTML() {
		$form = $this->renderForm();
		$this->tpl->setVariable('FREE_INPUT_FORM', $form);

		return $this->tpl->get();
	}
}