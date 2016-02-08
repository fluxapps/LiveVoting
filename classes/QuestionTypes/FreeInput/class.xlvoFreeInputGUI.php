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
	const F_VOTE_ID = 'vote_id';
	const CMD_CLEAR = 'clear';


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
		if ($this->manager->getVoting()->isMultiFreeInput()) {
			foreach ($_POST[self::F_VOTE_MULTI_LINE_INPUT] as $item) {
				$this->manager->input($item[self::F_FREE_INPUT], $item[self::F_VOTE_ID]);
			}
		} else {
			$this->manager->input($_POST[self::F_FREE_INPUT], $_POST[self::F_VOTE_ID]);
		}
	}


	protected function clear() {
		if ($this->manager->getVoting()->isMultiFreeInput()) {
			$this->manager->unvoteAll();
		} else {
			$this->manager->unvoteAll();
		}
		$this->afterSubmit();
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
	 * @return string
	 */
	protected function renderForm() {
		$votes = $this->manager->getVotesOfUser(true);
		$vote = array_shift(array_values($votes));
		$an = new ilTextInputGUI($this->txt('input'), self::F_FREE_INPUT);
		$an->setMaxLength(45);
		$hi2 = new ilHiddenInputGUI(self::F_VOTE_ID);

		if ($vote instanceof xlvoVote) {
			if ($vote->isActive()) {
				$an->setValue($vote->getFreeInput());
			}
			$hi2->setValue($vote->getId());
		}

		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setId('xlvo_free_input');
		$form->addItem($an);
		$form->addItem($hi2);
		$form->addCommandButton(self::CMD_SUBMIT, $this->txt('send'));
		$form->addCommandButton(self::CMD_CLEAR, $this->txt(self::CMD_CLEAR));

		return $form->getHTML();
	}


	/**
	 * @return string
	 */
	protected function renderMultiForm() {
		$mli = new xlvoMultiLineInputGUI($this->pl->txt('voter_answers'), self::F_VOTE_MULTI_LINE_INPUT);
		$te = new ilTextInputGUI($this->pl->txt('voter_text'), self::F_FREE_INPUT);
		$te->setMaxLength(45);

		$hi2 = new ilHiddenInputGUI(self::F_VOTE_ID);
		$mli->addInput($te);
		$mli->addInput($hi2);

		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->addCommandButton(self::CMD_SUBMIT, $this->pl->txt('voter_send'));
		$form->addCommandButton(self::CMD_CLEAR, $this->pl->txt('voter_delete_all'));
		$form->addItem($mli);
		$array = array();
		foreach ($this->manager->getVotesOfUser() as $xlvoVote) {
			$array[] = array(
				self::F_FREE_INPUT => $xlvoVote->getFreeInput(),
				self::F_VOTE_ID => $xlvoVote->getId()
			);
		}

		$form->setValuesByArray(array( self::F_VOTE_MULTI_LINE_INPUT => $array ));

		return $form->getHTML();
	}


	protected function render() {
		/**
		 * @var xlvoOption $option
		 */
		$option = $this->manager->getVoting()->getFirstVotingOption();
		$votes = $this->manager->getVotesOfUser();
		if ($this->manager->getVoting()->isMultiFreeInput()) {
			$form = $this->renderMultiForm($votes, $option);
		} else {
			$form = $this->renderForm($votes);
		}

		$this->tpl->setVariable('FREE_INPUT_FORM', $form);
	}
}
