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

		$bars = new xlvoBarMovableGUI($this->manager->getVoting()->getVotingOptions());

		$form2 = new ilPropertyFormGUI();
		$form2->addCommandButton(self::CMD_SUBMIT, $pl->txt('voter_save'));
		$form2->setOpenTag(false);

		return $form->getHTML() . $bars->getHTML() . $form2->getHTML();
	}


	public function initJS() {
		xlvoJs::getInstance()->api($this)->name('CorrectOrder')->category('QuestionTypes')->addLibToHeader('jquery.ui.touch-punch.min.js')->init();
	}


	protected function submit() {
		echo '<pre>' . print_r($_POST, 1) . '</pre>';
		exit;
		foreach ($_POST['id'] as $i => $id) {
		}
	}
}
