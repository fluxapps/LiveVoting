<?php

use LiveVoting\Js\xlvoJs;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoFreeInputGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
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
	 * @param bool $current
	 */
	public function initJS($current = false) {
		$xlvoMultiLineInputGUI = new xlvoMultiLineInputGUI();
		$xlvoMultiLineInputGUI->initCSSandJS();
		xlvoJs::getInstance()->api($this)->name(xlvoQuestionTypes::FREE_INPUT)->category('QuestionTypes')->init();
	}


	/**
	 *
	 */
	protected function submit() {
		$this->manager->unvoteAll();
		if ($this->manager->getVoting()->isMultiFreeInput()) {
			$array = array();
			foreach ($_POST[self::F_VOTE_MULTI_LINE_INPUT] as $item) {
				if ($item[self::F_FREE_INPUT] != "") {
					$array[] = array(
						"input" => $item[self::F_FREE_INPUT],
						"vote_id" => $item[self::F_VOTE_ID],
					);
				}
			}
			$this->manager->inputAll($array);
		} else {
			if ($_POST[self::F_FREE_INPUT] != "") {
				$this->manager->inputOne(array(
					"input" => $_POST[self::F_FREE_INPUT],
					"vote_id" => $_POST[self::F_VOTE_ID],
				));
			}
		}
	}


	/**
	 *
	 */
	protected function clear() {
		$this->manager->clear();
		$this->afterSubmit();
	}


	/**
	 * @return string
	 */
	public function getMobileHTML() {
		$this->tpl = $this->pl->getTemplate('default/Display/tpl.free_input.html');
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->render();

		return $this->tpl->get() . xlvoJs::getInstance()->name(xlvoQuestionTypes::FREE_INPUT)->category('QuestionTypes')->getRunCode();
	}


	/**
	 * @return string
	 */
	protected function renderForm() {
		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setId('xlvo_free_input');

		$votes = $this->manager->getVotesOfUser(true);
		$vote = array_shift(array_values($votes));

		$an = new ilTextInputGUI($this->txt('input'), self::F_FREE_INPUT);
		$an->setMaxLength(200);
		$hi2 = new ilHiddenInputGUI(self::F_VOTE_ID);

		if ($vote instanceof xlvoVote) {
			if ($vote->isActive()) {
				$an->setValue($vote->getFreeInput());
			}
			$hi2->setValue($vote->getId());
			$form->addCommandButton(self::CMD_CLEAR, $this->txt(self::CMD_CLEAR));
		}

		$form->addItem($an);
		$form->addItem($hi2);
		$form->addCommandButton(self::CMD_SUBMIT, $this->txt('send'));

		return $form->getHTML();
	}


	/**
	 * @return string
	 */
	protected function renderMultiForm() {
		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));

		$xlvoVotes = $this->manager->getVotesOfUser();
		if (count($xlvoVotes) > 0) {
			$te = new ilNonEditableValueGUI();
			$te->setValue($this->txt('your_input'));
			$form->addItem($te);
			$form->addCommandButton(self::CMD_CLEAR, $this->txt('delete_all'));
		}

		$mli = new xlvoMultiLineInputGUI($this->txt('answers'), self::F_VOTE_MULTI_LINE_INPUT);
		$te = new ilTextInputGUI($this->txt('text'), self::F_FREE_INPUT);
		$te->setMaxLength(45);

		$hi2 = new ilHiddenInputGUI(self::F_VOTE_ID);
		$mli->addInput($te);
		$mli->addInput($hi2);

		$form->addItem($mli);
		$array = array();
		foreach ($xlvoVotes as $xlvoVote) {
			$array[] = array(
				self::F_FREE_INPUT => $xlvoVote->getFreeInput(),
				self::F_VOTE_ID => $xlvoVote->getId(),
			);
		}

		$form->setValuesByArray(array( self::F_VOTE_MULTI_LINE_INPUT => $array ));
		$form->addCommandButton(self::CMD_SUBMIT, $this->txt('send'));

		return $form->getHTML();
	}


	/**
	 *
	 */
	protected function render() {
		if ($this->manager->getVoting()->isMultiFreeInput()) {
			$form = $this->renderMultiForm();
		} else {
			$form = $this->renderForm();
		}

		$this->tpl->setVariable('FREE_INPUT_FORM', $form);
	}
}
