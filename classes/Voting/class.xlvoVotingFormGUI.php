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

	const F_COLUMNS = 'columns';
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
		$te->addPlugin('latex');
		$te->addButton('latex');
		$te->addButton('pastelatex');
		$te->setRequired(true);
		$te->setRTESupport(ilObject::_lookupObjId($_GET['ref_id']), "dcl", "xlvo", null, false); // We have to prepend that this is a datacollection
		$te->setUseRte(true);
		$te->setRteTags(array(
			'p',
			'a',
			'br',
			'strong',
			'b',
			'i',
			'span',
			'img',
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
			'formatselect',
			'bullist',
			'hr',
			'sub',
			'sup',
			'numlist',
			'cite',
			//			'indent',
			//			'outdent',
		));

		$te->setRows(5);
		$this->addItem($te);

		// Columns
		if ($this->voting->getVotingType() != xlvoQuestionTypes::TYPE_FREE_INPUT) {
			$columns = new ilSelectInputGUI($this->txt(self::F_COLUMNS), self::F_COLUMNS);
			$columns->setOptions(array( 1 => 1, 2 => 2, 3 => 3, 4 => 4 ));
			$this->addItem($columns);
		}

		xlvoSubFormGUI::getInstance($this->getVoting())->appedElementsToForm($this);
	}


	/**
	 * @param $key
	 * @return string
	 */
	protected function txt($key) {
		return $this->parent_gui->txt($key);
	}


	public function fillForm() {
		$array = array(
			'type'          => $this->voting->getVotingType(),
			'title'         => $this->voting->getTitle(),
			'description'   => $this->voting->getDescription(),
			'question'      => $this->voting->getQuestionForEditor(),
			'voting_type'   => $this->voting->getVotingType(),
			'voting_status' => ($this->voting->getVotingStatus() == xlvoVoting::STAT_ACTIVE),
			self::F_COLUMNS => $this->voting->getColumns(),
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
		$this->voting->setQuestion(ilRTE::_replaceMediaObjectImageSrc($this->getInput('question'), 0));
		$this->voting->setObjId($this->parent_gui->getObjId());
		$this->voting->setColumns($this->getInput(self::F_COLUMNS));

		xlvoSubFormGUI::getInstance($this->getVoting())->handleAfterSubmit($this);

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
			$this->setTitle($this->parent_gui->txt('form_title_create'));
			$this->addCommandButton(xlvoVotingGUI::CMD_CREATE, $this->parent_gui->txt('create'));
		} else {
			$this->setTitle($this->parent_gui->txt('form_title_update'));
			$this->addCommandButton(xlvoVotingGUI::CMD_UPDATE, $this->parent_gui->txt('update'));
			$this->addCommandButton(xlvoVotingGUI::CMD_UPDATE_AND_STAY, $this->parent_gui->txt('update_and_stay'));
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