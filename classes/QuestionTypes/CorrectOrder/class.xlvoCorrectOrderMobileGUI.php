<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoInputMobileGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Display/Bar/class.xlvoBarMovableGUI.php');

/**
 * Class xlvoCorrectOrderMobileGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoCorrectOrderMobileGUI extends xlvoInputMobileGUI {

	/**
	 * @var int
	 */
	protected $answer_count = 64;


	/**
	 * xlvoInputMobileGUI constructor.
	 * @param xlvoVoting $voting
	 */
	public function __construct(xlvoVoting $voting) {
		parent::__construct($voting);
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$pl = ilLiveVotingPlugin::getInstance();
		$form = new ilPropertyFormGUI();
		$form->setId('xlvo_sortable');
		$form->setShowTopButtons(false);
		$form->setKeepOpen(true);

		$bars = new xlvoBarMovableGUI($this->voting->getVotingOptions());

		$form2 = new ilPropertyFormGUI();
		$form2->addCommandButton('#', $pl->txt('voter_save'));
		$form2->setOpenTag(false);

		return $form->getHTML() . $bars->getHTML() . $form2->getHTML();
	}
}
