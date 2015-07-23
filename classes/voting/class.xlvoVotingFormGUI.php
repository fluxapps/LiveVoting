<?php

require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('./Services/Form/classes/class.ilAdvSelectInputGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingType.php');

class xlvoVotingFormGUI extends ilPropertyFormGUI {

	/**
	 * @var  xlvoVoting
	 */
	protected $object;
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
	 * @param              $parent_gui
	 * @param xlvoVoting   $xlvoVoting
	 */
	public function __construct($parent_gui, xlvoVoting $xlvoVoting) {
		global $ilCtrl;
		/**
		 * @var $ilCtrl ilCtrl
		 */
		$this->object = $xlvoVoting;
		$this->parent_gui = $parent_gui;
		$this->ctrl = $ilCtrl;
		$this->pl = ilLiveVotingPlugin::getInstance();

		$this->ctrl->saveParameter($parent_gui, xlvoVotingGUI::IDENTIFIER);
		$this->is_new = ($this->object->getId() == '');

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
		$qu->setRTESupport($this->object->getId(), "xlvo", "xlvo_question", NULL, false, "3.4.7");
		$this->addItem($qu);
		$sl = new ilAdvSelectInputGUI();
		$sl->setTitle($this->pl->txt('voting_type'));
		$sl->addOption(xlvoVotingType::SINGLE_VOTE, $this->pl->txt('single_vote'), $this->pl->txt('single_vote'));
		$sl->addOption(xlvoVotingType::FREE_INPUT, $this->pl->txt('free_input'), $this->pl->txt('free_input'));
		$sl->setRequired(true);
		$this->addItem($sl);
		$cb = new ilCheckboxInputGUI($this->pl->txt('multi_selection'), 'multi_selection');
		$this->addItem($cb);
		$cb = new ilCheckboxInputGUI($this->pl->txt('colors'), 'colors');
		$this->addItem($cb);
		// TODO voting_options
	}

	public function fillForm() {
		if ($this->object->getObjId() == $this->parent_gui->getObjId()) {
			$array = array(
				'title' => $this->object->getTitle(),
				'description' => $this->object->getDescription(),
				'question' => $this->object->getQuestion(),
				'multi_selection' => $this->object->isMultiSelection(),
				'colors' => $this->object->isColors()
			);
			$this->setValuesByArray($array);
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

		$this->object->setTitle($this->getInput('title'));
		$this->object->setDescription($this->getInput('description'));
		$this->object->setQuestion($this->getInput('question'));
		$this->object->setMultiSelection($this->getInput('multi_selection'));
		$this->object->setColors($this->getInput('colors'));
		$this->object->setObjId($this->parent_gui->getObjId());
		$this->object->setVotingType(xlvoVotingType::SINGLE_VOTE);

		// TODO voting_options

		return true;
	}


	/**
	 * @return bool|string
	 */
	public function saveObject() {
		if (! $this->fillObject()) {
			return false;
		}
		if ($this->object->getObjId() == $this->parent_gui->getObjId()) {
			if (! xlvoVoting::where(array( 'id' => $this->object->getId() ))->hasSets()) {
				$this->object->create();
			} else {
				$this->object->update();
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
			$this->addCommandButton(xlvoVotingGUI::CMD_CREATE, $this->pl->txt('create'));
		} else {
			$this->setTitle($this->parent_gui->txt('update'));
			$this->addCommandButton(xlvoVotingGUI::CMD_UPDATE, $this->pl->txt('update'));
		}

		$this->addCommandButton(xlvoVotingGUI::CMD_CANCEL, $this->pl->txt('cancel'));
	}
}