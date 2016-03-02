<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoQuestionTypesGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarMovableGUI.php');

/**
 * Class xlvoCorrectOrderGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoCorrectOrderGUI: xlvoVoter2GUI
 */
class xlvoCorrectOrderGUI extends xlvoQuestionTypesGUI {

	/**
	 * @return string
	 */
	public function getMobileHTML() {
		return $this->getFormContent();
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


	/**
	 * @return string
	 */
	protected function getFormContent() {
		$pl = ilLiveVotingPlugin::getInstance();

		$tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/QuestionTypes/FreeOrder/tpl.free_order.html', true, false);
		$tpl->setVariable('ACTION', $this->ctrl->getFormAction($this));
		$tpl->setVariable('ID', 'xlvo_sortable');
		$tpl->setVariable('BTN_RESET', $pl->txt('qtype_4_clear'));
		$tpl->setVariable('BTN_SAVE', $pl->txt('qtype_4_save'));

		$vote = array_shift(array_values($this->manager->getVotesOfUser()));
		$order = array();
		$vote_id = null;
		if ($vote instanceof xlvoVote) {
			$order = json_decode($vote->getFreeInput());
			$vote_id = $vote->getId();
		}
		if (!$vote_id) {
			$tpl->setVariable('BTN_RESET_DISABLED', 'disabled="disabled"');
		}

		$bars = new xlvoBarMovableGUI($this->manager->getVoting()->getVotingOptions(), $order, $vote_id);
		$tpl->setVariable('CONTENT', $bars->getHTML());

		return $tpl->get();
	}
}
