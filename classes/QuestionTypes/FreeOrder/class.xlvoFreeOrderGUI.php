<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoQuestionTypesGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarMovableGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/CorrectOrder/class.xlvoCorrectOrderGUI.php');

/**
 * Class xlvoFreeOrderGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoFreeOrderGUI: xlvoVoter2GUI
 */
class xlvoFreeOrderGUI extends xlvoCorrectOrderGUI {




	public function initJS() {
		xlvoJs::getInstance()->api($this)->name('FreeOrder')->category('QuestionTypes')->addLibToHeader('jquery.ui.touch-punch.min.js')->init();
	}
}
