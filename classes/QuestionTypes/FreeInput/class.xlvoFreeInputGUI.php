<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoQuestionTypesGUI.php');
require_once('class.xlvoMultiLineInputGUI.php');
/**
 * Class xlvoFreeInputGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoFreeInputGUI: xlvoVoter2GUI
 */
class xlvoFreeInputGUI extends xlvoQuestionTypesGUI {

	const CMD_UNVOTE_ALL = 'unvoteAll';
	const CMD_SUBMIT = 'submit';
	const F_VOTE_MULTI_LINE_INPUT = 'vote_multi_line_input';
	const F_FREE_INPUT = 'free_input';


	/**
	 * @description add JS to the HEAD
	 */
	public function initJS() {
		$xlvoMultiLineInputGUI = new xlvoMultiLineInputGUI();
		$xlvoMultiLineInputGUI->initCSSandJS();
		xlvoJs::getInstance()->api($this)->name('CorrectOrder')->category('FreeInput')->init();
	}


	/**
	 * @description Vote
	 */
	protected function submit() {
		echo '<pre>' . print_r($_POST, 1) . '</pre>';


		exit;
	}


	/**
	 * @return string
	 */
	public function getMobileHTML() {
		$this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/Voting/display/tpl.free_input.html', true, true);
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->render();

		return $this->tpl->get();
	}





	/**
	 * @param xlvoVote $vote
	 *
	 * @return string
	 */
	protected function renderForm(xlvoVote $vote) {
		$an = new ilTextInputGUI($this->pl->txt('voter_answer'), self::F_FREE_INPUT);
		$an->setValue($vote->getFreeInput());
		$an->setMaxLength(45);

		$hi1 = new ilHiddenInputGUI('option_id');
		$hi1->setValue($vote->getOptionId());

		$hi2 = new ilHiddenInputGUI('vote_id');
		$hi2->setValue($vote->getId());

		$form = new ilPropertyFormGUI();
		$form->setId(self::F_FREE_INPUT);
		$form->addItem($an);
		$form->addItem($hi1);
		$form->addItem($hi2);
		$form->addCommandButton('send_unvote', $this->pl->txt('voter_delete'));
		$form->addCommandButton('send_vote', $this->pl->txt('voter_send'));

		return $form->getHTML();
	}


	/**
	 * @param array $votes
	 * @param xlvoOption $option
	 *
	 * @return string
	 */
	protected function renderMultiForm(array $votes, xlvoOption $option) {
		$mli = new xlvoMultiLineInputGUI($this->pl->txt('voter_answers'), self::F_VOTE_MULTI_LINE_INPUT);
		$te = new ilTextInputGUI($this->pl->txt('voter_text'), self::F_FREE_INPUT);
		$te->setMaxLength(45);
		$mli->addCustomAttribute('option_id', $option->getId());
		$mli->addInput($te);

		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->addCommandButton(self::CMD_UNVOTE_ALL, $this->pl->txt('voter_delete_all'));
		$form->addCommandButton(self::CMD_SUBMIT, $this->pl->txt('voter_send'));
		$form->addItem($mli);

		$array = array(
			self::F_VOTE_MULTI_LINE_INPUT => $votes
		);

		$form->setValuesByArray($array);

		return $form->getHTML();
	}


	protected function render() {
		/**
		 * @var xlvoOption $option
		 */
		$option = $this->voting->getFirstVotingOption();

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

}
