<?php

require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('./Services/Form/classes/class.ilAdvSelectInputGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingType.php');

class xlvoVotingFormGUI extends ilPropertyFormGUI {

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
	 * @var int
	 */
	protected $voting_type;
	/**
	 * @var int
	 */
	protected $voting_id;


	/**
	 * @param              $parent_gui
	 * @param xlvoVoting   $xlvoVoting
	 */
	public function __construct($parent_gui, xlvoVoting $xlvoVoting) {
		global $ilCtrl;
		/**
		 * @var $ilCtrl ilCtrl
		 */
		$this->voting = $xlvoVoting;
		$this->parent_gui = $parent_gui;
		$this->ctrl = $ilCtrl;
		$this->pl = ilLiveVotingPlugin::getInstance();

		$this->ctrl->saveParameter($parent_gui, xlvoVotingGUI::IDENTIFIER);
		$this->is_new = ($this->voting->getId() == '');

		$this->initForm();
	}


	protected function initForm() {
		$this->setTarget('_top');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initButtons();

		$te = new ilTextInputGUI($this->pl->txt('title'), 'title');
		$te->setRequired(true);
		$this->addItem($te);
		$ta = new ilTextAreaInputGUI($this->pl->txt('description'), 'description');
		$this->addItem($ta);
		$qu = new ilTextAreaInputGUI($this->pl->txt('question'), 'question');
		$qu->setRequired(true);
		$qu->setUseRte(true);
		$qu->usePurifier(true);
		$qu->setRTESupport($this->voting->getId(), "xlvo", "xlvo_question", NULL, false, "3.4.7");
		$this->addItem($qu);

		if ($this->is_new) {
			$sl = new ilAdvSelectInputGUI($this->pl->txt('type'), 'voting_type');
			$sl->addOption(xlvoVotingType::SINGLE_VOTE, $this->pl->txt('single_vote'), $this->pl->txt('single_vote'));
			$sl->addOption(xlvoVotingType::FREE_INPUT, $this->pl->txt('free_input'), $this->pl->txt('free_input'));
			$this->addItem($sl);
		}
		if (! $this->is_new && $this->voting->getVotingStatus() != xlvoVoting::STAT_INCOMPLETE) {
			$cb = new ilCheckboxInputGUI($this->pl->txt('active'), 'voting_status');
			$this->addItem($cb);
		}
	}


	public function fillForm() {
		if ($this->voting->getObjId() == $this->parent_gui->getObjId()) {
			$array = array(
				'title' => $this->voting->getTitle(),
				'description' => $this->voting->getDescription(),
				'question' => $this->voting->getQuestion(),
				'voting_type' => $this->voting->getVotingType(),
				'voting_status' => $this->voting->getVotingStatus()
			);
			$this->setValuesByArray($array);
			if ($this->voting->getVotingStatus() == xlvoVoting::STAT_INCOMPLETE) {
				ilUtil::sendInfo($this->pl->txt('msg_voting_not_complete'), false);
			}
		} else {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this->parent_gui, xlvoVotingGUI::CMD_STANDARD);
		}
	}


	/**
	 * returns whether checkinput was successful or not.
	 *
	 * @return bool
	 */
	public function fillObject() {
		if (! $this->checkInput()) {
			return false;
		}

		$this->voting->setTitle($this->getInput('title'));
		$this->voting->setDescription($this->getInput('description'));
		$this->voting->setQuestion($this->getInput('question'));
		$this->voting->setObjId($this->parent_gui->getObjId());

		if ($this->is_new) {
			$this->voting->setVotingStatus(xlvoVoting::STAT_INCOMPLETE);
			$this->voting->setVotingType($this->getInput('voting_type'));
		} else {
			if ($this->voting->getVotingStatus() != xlvoVoting::STAT_INCOMPLETE) {
				$this->voting->setVotingStatus($this->getInput('voting_status'));
			}
		}

		return true;
	}


	/**
	 * @return bool|string
	 */
	public function saveObject() {
		if (! $this->fillObject()) {
			return false;
		}

		if ($this->voting->getObjId() == $this->parent_gui->getObjId()) {
			if (! xlvoVoting::where(array( 'id' => $this->voting->getId() ))->hasSets()) {
				$this->voting->create();
			} else {
				$this->voting->update();
			}
		} else {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this->parent_gui, xlvoVoting::CMD_STANDARD);
		}

		return true;
	}


	protected function initButtons() {
		if ($this->is_new) {
			$this->setTitle($this->pl->txt('create'));
			$this->addCommandButton(xlvoVotingGUI::CMD_CREATE, $this->pl->txt('next'));
		} else {
			$this->setTitle($this->pl->txt('update'));
			$this->addCommandButton(xlvoVotingGUI::CMD_UPDATE, $this->pl->txt('next'));
		}

		$this->addCommandButton(xlvoVotingGUI::CMD_CANCEL, $this->pl->txt('cancel'));
	}


	/**
	 * @return xlvoVoting
	 */
	public function getVoting() {
		return $this->voting;
	}


	/**
	 * @param xlvoVoting $voting
	 */
	public function setVoting($voting) {
		$this->voting = $voting;
	}
}