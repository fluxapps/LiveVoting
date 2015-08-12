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
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;


	public function __construct(xlvoVoting $voting) {
		global $tpl;
		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/vote_freeinput.js');

		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/tpl.free_input.html', false, false);
		$this->voting = $voting;
		$this->voting_manager = new xlvoVotingManager();
		$this->pl = ilLiveVotingPlugin::getInstance();
	}


	protected function renderForm(xlvoVote $vote) {

		$an = new ilTextInputGUI($this->pl->txt('answer'), 'free_input');
		$an->setValue($vote->getFreeInput());
		$an->setRequired(true);

		$hi1 = new ilHiddenInputGUI('option_id');
		$hi1->setValue($vote->getOptionId());

		$hi2 = new ilHiddenInputGUI('vote_id');
		$hi2->setValue($vote->getId());

		$form = new ilPropertyFormGUI();
		$form->addItem($an);
		$form->addItem($hi1);
		$form->addItem($hi2);
		$form->addCommandButton('send_unvote', $this->pl->txt('delete'));
		$form->addCommandButton('send_vote', $this->pl->txt('send'));

		return $form->getHTML();
	}


	protected function renderMultiForm(array $votes, xlvoOption $option) {
		$mli = new xlvoMultiLineInputGUI($this->pl->txt('answers'), 'vote_multi_line_input');
		$te = new ilTextInputGUI($this->pl->txt('text'), 'free_input');
		$mli->addCustomAttribute('option_id', $option->getId());
		$mli->addInput($te);

		$form = new ilPropertyFormGUI();
		$form->addCommandButton('unvote_all', $this->pl->txt('delete_all'));
		$form->addCommandButton('send_votes', $this->pl->txt('send'));
		$form->addItem($mli);

		$array = array(
			'vote_multi_line_input' => $votes
		);

		$form->setValuesByArray($array);

		return $form->getHTML();
	}


	protected function render() {
		$option = $this->voting_manager->getOptionsForVoting($this->voting->getId())->first();

		if ($this->voting->isMultiFreeInput()) {
			$votes = $this->voting_manager->getVotes($this->voting->getId(), $option->getId(), true)->getArray();
			$form = $this->renderMultiForm($votes, $option);
		} else {
			$vote = $this->voting_manager->getVotes($this->voting->getId(), $option->getId(), true)->first();
			if (! $vote instanceof xlvoVote) {
				$vote = new xlvoVote();
				$vote->setOptionId($option->getId());
			}
			$form = $this->renderForm($vote);
		}

		$this->tpl->setVariable('FREE_INPUT_FORM', $form);
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$this->render();

		return $this->tpl->get();
	}
}