<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoInputMobileGUI.php');

/**
 * Class xlvoFreeInputMobileGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoFreeInputMobileGUI extends xlvoInputMobileGUI {

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
	/**
	 * @var xlvoVotingManager
	 */
	protected $voting_manager;


	/**
	 * @param xlvoVote $vote
	 *
	 * @return string
	 */
	protected function renderForm(xlvoVote $vote) {

		$an = new ilTextInputGUI($this->pl->txt('answer'), 'free_input');
		$an->setValue($vote->getFreeInput());
		$an->setMaxLength(45);

		$hi1 = new ilHiddenInputGUI('option_id');
		$hi1->setValue($vote->getOptionId());

		$hi2 = new ilHiddenInputGUI('vote_id');
		$hi2->setValue($vote->getId());

		$form = new ilPropertyFormGUI();
		$form->setId('free_input');
		$form->addItem($an);
		$form->addItem($hi1);
		$form->addItem($hi2);
		$form->addCommandButton('send_unvote', $this->pl->txt('delete'));
		$form->addCommandButton('send_vote', $this->pl->txt('send'));

		return $form->getHTML();
	}


	/**
	 * @param array $votes
	 * @param xlvoOption $option
	 *
	 * @return string
	 */
	protected function renderMultiForm(array $votes, xlvoOption $option) {
		$mli = new xlvoMultiLineInputGUI($this->pl->txt('answers'), 'vote_multi_line_input');
		$te = new ilTextInputGUI($this->pl->txt('text'), 'free_input');
		$te->setMaxLength(45);
		$mli->addCustomAttribute('option_id', $option->getId());
		$mli->addInput($te);

		$form = new ilPropertyFormGUI();
		$form->setId('free_input_multi');
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
		/**
		 * @var xlvoOption $option
		 */
		$option = $this->voting->getVotingOptions()->first();

		if ($this->voting->isMultiFreeInput()) {
			/**
			 * @var xlvoVote[] $votes
			 */
			$votes = $this->voting_manager->getVotesOfUserOfOption($this->voting->getId(), $option->getId())->getArray();
			$form = $this->renderMultiForm($votes, $option);
		} else {
			/**
			 * @var xlvoVote $vote
			 */
			$vote = $this->voting_manager->getVotesOfUserOfOption($this->voting->getId(), $option->getId())->first();
			if (!$vote instanceof xlvoVote) {
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
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voting/display/tpl.free_input.html', true, true);
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->voting_manager = new xlvoVotingManager();
		$this->render();

		return $this->tpl->get();
	}
}
