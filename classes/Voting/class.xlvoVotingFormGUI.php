<?php

require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('./Services/Form/classes/class.ilAdvSelectInputGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoQuestionTypes.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoSubFormGUI.php');

/**
 * Class xlvoVotingFormGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
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
	 * @param xlvoVotingGUI $parent_gui
	 * @param xlvoVoting $xlvoVoting
	 */
	public function __construct(xlvoVotingGUI $parent_gui, xlvoVoting $xlvoVoting) {
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
		$h = new ilHiddenInputGUI('type');
		$this->addItem($h);

		$this->setTarget('_top');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initButtons();

		$te = new ilTextInputGUI($this->parent_gui->txt('title'), 'title');
		//		$te->setInfo($this->parent_gui->txt('info_voting_title'));
		$te->setRequired(true);
		$this->addItem($te);

		$ta = new ilTextAreaInputGUI($this->parent_gui->txt('description'), 'description');
		//		$ta->setInfo($this->parent_gui->txt('info_voting_description'));
		//		$this->addItem($ta);

		$te = new ilTextAreaInputGUI($this->parent_gui->txt('question'), 'question');
		$te->setRequired(true);
		$te->setUseRte(true);
		$te->setRteTags(array(
			'p',
			'a',
			'br',
			'strong',
			'b',
			'i',
		));
		$te->usePurifier(true);
		$te->disableButtons(array(
			'charmap',
			'undo',
			'redo',
			'justifyleft',
			'justifycenter',
			'justifyright',
			'justifyfull',
			'anchor',
			'fullscreen',
			'cut',
			'copy',
			'paste',
			'pastetext',
			'formatselect'
		));

		$te->setRows(5);
		$this->addItem($te);

		xlvoSubFormGUI::getInstance($this->getVoting())->appedElementsToForm($this);
	}


	public function fillForm() {
		$array = array(
			'type' => $this->voting->getVotingType(),
			'title' => $this->voting->getTitle(),
			'description' => $this->voting->getDescription(),
			'question' => $this->voting->getQuestion(),
			'voting_type' => $this->voting->getVotingType(),
			'voting_status' => ($this->voting->getVotingStatus() == xlvoVoting::STAT_ACTIVE)
		);

		$array = xlvoSubFormGUI::getInstance($this->getVoting())->appendValues($array);

		$this->setValuesByArray($array);
		if ($this->voting->getVotingStatus() == xlvoVoting::STAT_INCOMPLETE) {
			ilUtil::sendInfo($this->parent_gui->txt('msg_voting_not_complete'), false);
		}
	}


	/**
	 * @return bool
	 */
	public function fillObject() {
		if (!$this->checkInput()) {
			return false;
		}

		$this->voting->setVotingType($this->getInput('type'));
		$this->voting->setTitle($this->getInput('title'));
		$this->voting->setDescription($this->getInput('description'));
		$this->voting->setQuestion($this->getInput('question'));
		$this->voting->setObjId($this->parent_gui->getObjId());

		xlvoSubFormGUI::getInstance($this->getVoting())->handleAfterSubmit($this);

		if ($this->is_new) {
			$lastVoting = xlvoVoting::where(array(
				'obj_id' => $this->parent_gui->getObjId(),
				'voting_status' => xlvoVoting::STAT_ACTIVE
			))->orderBy('position', 'ASC')->last();
			if ($lastVoting instanceof xlvoVoting) {
				$lastPosition = $lastVoting->getPosition();
			} else {
				$lastPosition = 0;
			}
			$this->voting->setPosition($lastPosition + 1);
		}

		return true;
	}


	/**
	 * @return bool
	 */
	public function saveObject() {
		if (!$this->fillObject()) {
			return false;
		}

		if ($this->voting->getObjId() == $this->parent_gui->getObjId()) {
			$this->voting->store();
			xlvoSubFormGUI::getInstance($this->getVoting())->handleAfterCreation($this->voting);
		} else {
			ilUtil::sendFailure($this->parent_gui->txt('permission_denied_object'), true);
			$this->ctrl->redirect($this->parent_gui, xlvoVotingGUI::CMD_STANDARD);
		}

		return true;
	}


	protected function initButtons() {
		if ($this->is_new) {
			$this->setTitle($this->parent_gui->txt('create'));
			$this->addCommandButton(xlvoVotingGUI::CMD_CREATE, $this->parent_gui->txt('create'));
		} else {
			$this->setTitle($this->parent_gui->txt('update'));
			$this->addCommandButton(xlvoVotingGUI::CMD_UPDATE, $this->parent_gui->txt('update'));
		}

		$this->addCommandButton(xlvoVotingGUI::CMD_CANCEL, $this->parent_gui->txt('cancel'));
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