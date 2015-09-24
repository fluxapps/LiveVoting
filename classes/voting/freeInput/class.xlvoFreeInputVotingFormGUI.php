<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoMultiLineInputGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');

/**
 * Class xlvoFreeInputVotingFormGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoFreeInputVotingFormGUI extends xlvoVotingFormGUI {

	/**
	 * @var  xlvoVoting
	 */
	protected $voting;
	/**
	 * @var xlvoVotingGUI
	 */
	protected $parent_gui;
	/**
	 * @var  ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;
	/**
	 * @var boolean
	 */
	protected $is_new;
	/**
	 * @var xlvoVotingManager
	 */
	protected $voting_manager;
	/**
	 * @var xlvoOption[]
	 */
	protected $options;
	/**
	 * @var boolean
	 */
	protected $has_existing_votes;


	/**
	 * @param xlvoFreeInputVotingGUI $parent_gui
	 * @param xlvoVoting             $xlvoVoting
	 */
	public function __construct(xlvoFreeInputVotingGUI $parent_gui, xlvoVoting $xlvoVoting) {
		global $ilCtrl;
		/**
		 * @var $ilCtrl ilCtrl
		 */
		$this->voting = $xlvoVoting;
		$this->parent_gui = $parent_gui;
		$this->ctrl = $ilCtrl;
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->ctrl->saveParameter($parent_gui, xlvoVotingGUI::IDENTIFIER);
		$this->is_new = ($this->voting->getVotingStatus() == xlvoVoting::STAT_INCOMPLETE);
		$this->voting_manager = new xlvoVotingManager();
		$this->has_existing_votes = (xlvoVote::where(array( 'voting_id' => $this->voting->getId() ))->count() > 0);

		$this->initForm();
	}


	protected function initForm() {
		$this->setTarget('_top');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initButtons();

		if (! $this->has_existing_votes) {
			$cb = new ilCheckboxInputGUI($this->pl->txt('multi_free_input'), 'multi_free_input');
			$cb->setInfo($this->pl->txt('info_freeinput_multi_free_input'));
			$this->addItem($cb);
		} else {
			$cb = new ilNonEditableValueGUI($this->pl->txt('multi_free_input'));
			$cb->setValue($this->pl->txt('msg_multi_free_input_not_editable'));
			$this->addItem($cb);
		}
	}


	public function fillForm() {
		if ($this->voting->getObjId() == $this->parent_gui->getObjId()) {
			$this->options = xlvoOption::where(array(
				'voting_id' => $this->voting->getId(),
				'status' => xlvoOption::STAT_ACTIVE
			))->getArray();

			// only set these values if no votes exist
			if (! $this->has_existing_votes) {
				$array = array(
					'multi_free_input' => $this->voting->isMultiFreeInput()
				);
				$this->setValuesByArray($array);
			}

			if ($this->voting->getVotingStatus() == xlvoVoting::STAT_INCOMPLETE) {
				ilUtil::sendInfo($this->pl->txt('voting_not_complete'), false);
			}
		} else {
			ilUtil::sendFailure($this->pl->txt('permission_denied_object'), true);
			$this->ctrl->redirect($this->parent_gui, xlvoVotingGUI::CMD_STANDARD);
		}
	}


	/**
	 * @return bool
	 */
	public function fillObject() {
		if (! $this->checkInput()) {
			return false;
		}

		// only set this attribut if no votes exist
		if(! $this->has_existing_votes) {
			$this->voting->setMultiFreeInput($this->getInput('multi_free_input'));
		}

		$this->options = xlvoOption::where(array(
			'voting_id' => $this->voting->getId(),
			'status' => xlvoOption::STAT_ACTIVE
		))->getArray();

		return true;
	}


	/**
	 * @return bool
	 */
	public function saveObject() {
		if (! $this->fillObject()) {
			return false;
		}
		if ($this->voting->getObjId() == $this->parent_gui->getObjId()) {

			if (xlvoVoting::where(array( 'id' => $this->voting->getId() ))->hasSets()) {

				if (count(xlvoOption::where(array( 'voting_id' => $this->voting->getId() ))->getArray()) <= 0) {
					/**
					 * @var $xlvoOption xlvoOption
					 */
					$xlvoOption = new xlvoOption();
					$xlvoOption->setVotingId($this->voting->getId());
					$xlvoOption->setType($this->voting->getVotingType());
					$xlvoOption->setStatus(xlvoOption::STAT_ACTIVE);
					$xlvoOption->setText('FREE_INPUT');
					$xlvoOption->create();
				}

				if ($this->voting->getVotingStatus() == xlvoVoting::STAT_INCOMPLETE) {
					$this->voting->setVotingStatus(xlvoVoting::STAT_ACTIVE);
				}

				$this->voting->update();
			}
		} else {
			ilUtil::sendFailure($this->pl->txt('permission_denied_object'), true);
			$this->ctrl->redirect($this->parent_gui, xlvoVotingGUI::CMD_STANDARD);
		}

		return true;
	}


	protected function initButtons() {
		$this->addCommandButton(xlvoVotingGUI::CMD_BACK, $this->pl->txt('back'));

		if ($this->is_new) {
			$this->setTitle($this->pl->txt('create'));
			$this->addCommandButton(xlvoVotingGUI::CMD_CREATE, $this->pl->txt('create'));
		} else {
			$this->setTitle($this->pl->txt('update'));
			$this->addCommandButton(xlvoVotingGUI::CMD_UPDATE, $this->pl->txt('update'));
		}

		$this->addCommandButton(xlvoVotingGUI::CMD_CANCEL, $this->pl->txt('cancel'));
	}
}