<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoQuestionTypesGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarMovableGUI.php');

/**
 * Class xlvoCorrectOrderGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoCorrectOrderGUI: xlvoVoter2GUI
 */
class xlvoCorrectOrderGUI extends xlvoQuestionTypesGUI {

	/**
	 * @return string
	 */
	public function getMobileHTML() {
		$pl = ilLiveVotingPlugin::getInstance();
		$form = new ilPropertyFormGUI();
		$form->setId('xlvo_sortable');
		$form->setShowTopButtons(false);
		$form->setKeepOpen(true);
		$form->setFormAction($this->ctrl->getFormAction($this));
		/**
		 * @var $vote xlvoVote
		 */
		$vote = array_shift(array_values($this->manager->getVotesOfUser()));
		$order = array();
		$vote_id = null;
		if ($vote instanceof xlvoVote) {
			$order = json_decode($vote->getFreeInput());
			$vote_id = $vote->getId();
		}
		$bars = new xlvoBarMovableGUI($this->manager->getVoting()->getVotingOptions(), $order, $vote_id);

		$form2 = new ilPropertyFormGUI();
		$form2->addCommandButton(self::CMD_SUBMIT, $pl->txt('qtype_4_save'));
		if ($vote_id) {
			$form2->addCommandButton('clear', $pl->txt('qtype_4_clear'));
		}
		$form2->setOpenTag(false);

		return $form->getHTML() . $bars->getHTML() . $form2->getHTML();
	}


	public function initJS() {
		xlvoJs::getInstance()->api($this)->name('CorrectOrder')->category('QuestionTypes')->addLibToHeader('jquery.ui.touch-punch.min.js')->init();
	}


	protected function submit() {
		$this->manager->input(json_encode($_POST['id']), $_POST['vote_id']);
	}


	protected function clear() {
		$this->manager->unvoteAll();
		$this->afterSubmit();
	}
}
