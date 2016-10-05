<?php

require_once('./Services/Object/classes/class.ilObject2.php');
require_once('./Services/Utilities/classes/class.ilConfirmationGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVotingFormGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVoting.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVotingTableGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/class.xlvoQuestionTypes.php');

/**
 *
 * Class xlvoVotingGUI
 *
 * @author            Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_Calls      xlvoVotingGUI: xlvoSingleVoteVotingGUI, xlvoFreeInputVotingGUI
 *
 */
class xlvoVotingGUI {

	const IDENTIFIER = 'xlvoVot';
	const CMD_STANDARD = 'content';
	const CMD_CONTENT = 'content';
	const CMD_ADD = 'add';
	const CMD_SELECT_TYPE = 'selectType';
	const CMD_CREATE = 'create';
	const CMD_EDIT = 'edit';
	const CMD_UPDATE = 'update';
	const CMD_UPDATE_AND_STAY = 'updateAndStay';
	const CMD_CONFIRM_DELETE = 'confirmDelete';
	const CMD_DELETE = 'delete';
	const CMD_CONFIRM_RESET = 'confirmReset';
	const CMD_DUPLICATE = 'duplicate';
	const CMD_RESET = 'reset';
	const CMD_CONFIRM_RESET_ALL = 'confirmResetAll';
	const CMD_RESET_ALL = 'resetAll';
	const CMD_CANCEL = 'cancel';
	const CMD_BACK = 'back';
	const F_TYPE = 'type';
	/**
	 * @var ilTemplate
	 */
	public $tpl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;
	/**
	 * @var ilObjLiveVotingAccess
	 */
	protected $access;
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;
	/**
	 * @var ilObjUser
	 */
	protected $usr;
	/**
	 * @var int
	 */
	protected $obj_id;
	/**
	 * @var xlvoVotingManager
	 */
	protected $voting_manager;

	/**
	 * @var xlvoRound
	 */
	protected $round;


	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs, $ilUser, $ilToolbar;

		/**
		 * @var $tpl       ilTemplate
		 * @var $ilCtrl    ilCtrl
		 * @var $ilTabs    ilTabsGUI
		 * @var $ilUser    ilObjUser
		 * @var $ilToolbar ilToolbarGUI
		 */
		$this->tpl = $tpl;
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->usr = $ilUser;
		$this->toolbar = $ilToolbar;
		$this->access = new ilObjLiveVotingAccess();
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->obj_id = ilObject2::_lookupObjId($_GET['ref_id']);
		$this->round = xlvoRound::getLatestRound($this->obj_id);
	}


	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
				$this->{$cmd}();
				break;
		}
	}


	protected function content() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
		} else {
			if ($this->access->hasWriteAccess()) {
				$b = ilLinkButton::getInstance();
				$b->setPrimary(true);
				$b->setCaption($this->txt('add'), false);
				$b->setUrl($this->ctrl->getLinkTarget(new xlvoVotingGUI(), self::CMD_SELECT_TYPE));
				$this->toolbar->addButtonInstance($b);

				$voting_ids = xlvoVoting::where(array( 'obj_id' => $this->obj_id ))->getArray(null, 'id');
				$has_votes = false;
				if (count($voting_ids) > 0) {
					$has_votes = xlvoVote::where(array( 'voting_id' => $voting_ids, 'round_id' => $this->round->getId()))->hasSets();
				}

				$b = ilLinkButton::getInstance();
				$b->setDisabled(!$has_votes);
				$b->setCaption($this->txt('reset_all'), false);
				$b->setUrl($this->ctrl->getLinkTarget(new xlvoVotingGUI(), self::CMD_CONFIRM_RESET_ALL));
				$this->toolbar->addButtonInstance($b);

				if ($_GET['import']) {
					$b = ilLinkButton::getInstance();
					$b->setCaption($this->txt('export'), false);
					$b->setUrl($this->ctrl->getLinkTarget(new xlvoVotingGUI(), 'export'));
					$this->toolbar->addButtonInstance($b);

					$this->toolbar->setFormAction($this->ctrl->getLinkTarget($this, 'import'), true);
					$import = new ilFileInputGUI('xlvo_import', 'xlvo_import');
					$this->toolbar->addInputItem($import);
					$this->toolbar->addFormButton($this->txt('import'), 'import');
				}

				$xlvoVotingTableGUI = new xlvoVotingTableGUI($this, self::CMD_STANDARD);
				$this->tpl->setContent($xlvoVotingTableGUI->getHTML());
			}
		}
	}


	protected function selectType() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$form = new ilPropertyFormGUI();
			$form->setFormAction($this->ctrl->getFormAction($this, self::CMD_ADD));
			$form->addCommandButton(self::CMD_ADD, $this->txt('select_type'));
			$form->addCommandButton(self::CMD_CANCEL, $this->txt('cancel'));
			$cb = new ilRadioGroupInputGUI($this->txt('type'), self::F_TYPE);
			$cb->setRequired(true);
			foreach (xlvoQuestionTypes::getActiveTypes() as $active_type) {
				$op = new ilRadioOption();
				$op->setTitle($this->txt('type_' . $active_type));
				$op->setInfo($this->txt('type_' . $active_type . '_info'));
				$op->setValue($active_type);
				$cb->addOption($op);
			}
			$form->addItem($cb);

			$this->tpl->setContent($form->getHTML());
		}
	}


	protected function add() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$xlvoVoting = new xlvoVoting();
			$xlvoVoting->setVotingType($_POST[self::F_TYPE]);
			$xlvoVotingFormGUI = new xlvoVotingFormGUI($this, $xlvoVoting);
			$xlvoVotingFormGUI->fillForm();
			$this->tpl->setContent($xlvoVotingFormGUI->getHTML());
		}
	}


	protected function create() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$xlvoVoting = new xlvoVoting();
			$xlvoVoting->setVotingType($_POST[self::F_TYPE]);
			$xlvoVotingFormGUI = new xlvoVotingFormGUI($this, $xlvoVoting);
			$xlvoVotingFormGUI->setValuesByPost();
			if ($xlvoVotingFormGUI->saveObject()) {
				ilUtil::sendSuccess($this->pl->txt('msg_success_voting_created'), true);
				$this->ctrl->redirect($this, self::CMD_STANDARD);
			}
			$this->tpl->setContent($xlvoVotingFormGUI->getHTML());
		}
	}


	protected function edit() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			/**
			 * @var $xlvoVoting xlvoVoting
			 */
			$xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);
			// PREV
			$prev_id = xlvoVoting::where(array(
				'obj_id'        => $xlvoVoting->getObjId(),
				'voting_status' => xlvoVoting::STAT_ACTIVE,
			))->orderBy('position', 'DESC')->where(array( 'position' => $xlvoVoting->getPosition() ), '<')->limit(0, 1)->getArray('id', 'id');
			$prev_id = array_shift(array_values($prev_id));

			if ($prev_id) {
				$this->ctrl->setParameter($this, self::IDENTIFIER, $prev_id);
				$prev = ilLinkButton::getInstance();
				$prev->setUrl($this->ctrl->getLinkTarget($this, self::CMD_EDIT));
				$prev->setCaption(xlvoGlyphGUI::get(xlvoGlyphGUI::PREVIOUS), false);
				$this->toolbar->addButtonInstance($prev);
			}

			// NEXT
			$next_id = xlvoVoting::where(array(
				'obj_id'        => $xlvoVoting->getObjId(),
				'voting_status' => xlvoVoting::STAT_ACTIVE,
			))->orderBy('position', 'ASC')->where(array( 'position' => $xlvoVoting->getPosition() ), '>')->limit(0, 1)->getArray('id', 'id');
			$next_id = array_shift(array_values($next_id));

			if ($next_id) {
				$this->ctrl->setParameter($this, self::IDENTIFIER, $next_id);
				$next = ilLinkButton::getInstance();
				$next->setUrl($this->ctrl->getLinkTarget($this, self::CMD_EDIT));
				$next->setCaption(xlvoGlyphGUI::get(xlvoGlyphGUI::NEXT), false);
				$this->toolbar->addButtonInstance($next);
			}
			$this->ctrl->setParameter($this, self::IDENTIFIER, $xlvoVoting->getId());
			$xlvoVotingFormGUI = new xlvoVotingFormGUI($this, $xlvoVoting);
			$xlvoVotingFormGUI->fillForm();
			$this->tpl->setContent($xlvoVotingFormGUI->getHTML());
		}
	}


	public function updateAndStay() {
		$this->update(self::CMD_EDIT);
	}


	/**
	 * @param string $cmd
	 */
	protected function update($cmd = self::CMD_STANDARD) {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$xlvoVotingFormGUI = new xlvoVotingFormGUI($this, xlvoVoting::find($_GET[self::IDENTIFIER]));
			$xlvoVotingFormGUI->setValuesByPost();
			if ($xlvoVotingFormGUI->saveObject()) {
				ilUtil::sendSuccess($this->pl->txt('msg_success_voting_updated'), true);
				$this->ctrl->redirect($this, $cmd);
			}
			$this->tpl->setContent($xlvoVotingFormGUI->getHTML());
		}
	}


	protected function confirmDelete() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {

			/**
			 * @var $xlvoVoting xlvoVoting
			 */
			$xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);

			if ($xlvoVoting->getObjId() == $this->getObjId()) {
				ilUtil::sendQuestion($this->txt('delete_confirm'), true);
				$confirm = new ilConfirmationGUI();
				$confirm->addItem(self::IDENTIFIER, $xlvoVoting->getId(), $xlvoVoting->getTitle());
				$confirm->setFormAction($this->ctrl->getFormAction($this));
				$confirm->setCancel($this->txt('cancel'), self::CMD_CANCEL);
				$confirm->setConfirm($this->txt('delete'), self::CMD_DELETE);

				$this->tpl->setContent($confirm->getHTML());
			} else {
				ilUtil::sendFailure($this->pl->txt('permission_denied_object'), true);
				$this->ctrl->redirect($this, self::CMD_STANDARD);
			}
		}
	}


	protected function delete() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {

			/**
			 * @var $xlvoVoting xlvoVoting
			 */
			$xlvoVoting = xlvoVoting::find($_POST[self::IDENTIFIER]);

			if ($xlvoVoting->getObjId() == $this->getObjId()) {
				/**
				 * @var $options xlvoOption[]
				 */
				$options = xlvoOption::where(array( 'voting_id' => $xlvoVoting->getId() ))->get();
				foreach ($options as $option) {
					$option->delete();
				}
				/**
				 * @var $votes xlvoVote[]
				 */
				$votes = xlvoVote::where(array( 'voting_id' => $xlvoVoting->getId()))->get();
				foreach ($votes as $vote) {
					$vote->delete();
				}
				$xlvoVoting->delete();
				$this->cancel();
			} else {
				ilUtil::sendFailure($this->pl->txt('delete_failed'), true);
				$this->ctrl->redirect($this, self::CMD_STANDARD);
			}
		}
	}


	protected function confirmReset() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {

			/**
			 * @var $xlvoVoting xlvoVoting
			 */
			$xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);

			if ($xlvoVoting->getObjId() == $this->getObjId()) {
				ilUtil::sendQuestion($this->txt('confirm_reset'), true);
				$confirm = new ilConfirmationGUI();
				$confirm->addItem(self::IDENTIFIER, $xlvoVoting->getId(), $xlvoVoting->getTitle());
				$confirm->setFormAction($this->ctrl->getFormAction($this));
				$confirm->setCancel($this->txt('cancel'), self::CMD_CANCEL);
				$confirm->setConfirm($this->txt('reset'), self::CMD_RESET);

				$this->tpl->setContent($confirm->getHTML());
			} else {
				ilUtil::sendFailure($this->txt('permission_denied_object'), true);
				$this->ctrl->redirect($this, self::CMD_STANDARD);
			}
		}
	}


	protected function reset() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			/**
			 * @var $xlvoVoting xlvoVoting
			 */
			$xlvoVoting = xlvoVoting::find($_POST[self::IDENTIFIER]);

			if ($xlvoVoting->getObjId() == $this->getObjId()) {

				/**
				 * @var $votes xlvoVote[]
				 */
				$votes = xlvoVote::where(array( 'voting_id' => $xlvoVoting->getId() ))->get();
				foreach ($votes as $vote) {
					$vote->delete();
				}
				$this->cancel();
			} else {
				ilUtil::sendFailure($this->pl->txt('reset_failed'), true);
				$this->ctrl->redirect($this, self::CMD_STANDARD);
			}
		}
	}


	protected function confirmResetAll() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			ilUtil::sendQuestion($this->txt('confirm_reset_all'), true);
			$confirm = new ilConfirmationGUI();
			/**
			 * @var $votings xlvoVoting[]
			 */
			$votings = xlvoVoting::where(array( 'obj_id' => $this->getObjId() ))->get();
			$num_votes = 0;
			foreach ($votings as $voting) {
				$num_votes += xlvoVote::where(array( 'voting_id' => $voting->getId() ))->count();
			}
			$confirm->addItem(self::IDENTIFIER, 0, $this->txt('confirm_number_of_votes') . " " . $num_votes);
			$confirm->setFormAction($this->ctrl->getFormAction($this));
			$confirm->setCancel($this->txt('cancel'), self::CMD_CANCEL);
			$confirm->setConfirm($this->txt('reset_all'), self::CMD_RESET_ALL);

			$this->tpl->setContent($confirm->getHTML());
		}
	}


	protected function resetAll() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			/**
			 * @var $votings xlvoVoting[]
			 */
			$votings = xlvoVoting::where(array( 'obj_id' => $this->getObjId() ))->get();
			foreach ($votings as $voting) {
				/**
				 * @var $votes xlvoVote[]
				 */
				$votes = xlvoVote::where(array( 'voting_id' => $voting->getId() ))->get();
				foreach ($votes as $vote) {
					$vote->delete();
				}
			}

			$this->cancel();
		}
	}


	protected function duplicate() {
		/**
		 * @var $xlvoVoting xlvoVoting
		 */
		$xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);
		$xlvoVoting->fullClone(true, true);
		ilUtil::sendSuccess($this->pl->txt('voting_msg_duplicated'), true);
		$this->cancel();
	}


	protected function cancel() {
		$this->ctrl->redirect($this, self::CMD_STANDARD);
	}


	protected function saveSorting() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied_write'), true);
		} else {
			if (is_array($_POST['position'])) {
				foreach ($_POST['position'] as $k => $v) {
					/**
					 * @var $xlvoVoting xlvoVoting
					 */
					$xlvoVoting = xlvoVoting::find($v);
					$xlvoVoting->setPosition($k + 1);
					$xlvoVoting->update();
				}
			}
			ilUtil::sendSuccess($this->pl->txt('voting_msg_sorting_saved'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		}
	}


	protected function applyFilter() {
		$xlvoVotingGUI = new xlvoVotingTableGUI($this, self::CMD_STANDARD);
		$xlvoVotingGUI->writeFilterToSession();
		$this->ctrl->redirect($this, self::CMD_STANDARD);
	}


	protected function resetFilter() {
		$xlvoVotingTableGUI = new xlvoVotingTableGUI($this, self::CMD_STANDARD);
		$xlvoVotingTableGUI->resetFilter();
		$xlvoVotingTableGUI->resetOffset();
		$this->ctrl->redirect($this, self::CMD_STANDARD);
	}


	/**
	 * @return int
	 */
	public function getObjId() {
		return $this->obj_id;
	}


	/**
	 * @param int $obj_id
	 */
	public function setObjId($obj_id) {
		$this->obj_id = $obj_id;
	}


	/**
	 * @param $key
	 * @return string
	 */
	public function txt($key) {
		return $this->pl->txt('voting_' . $key);
	}


	protected function export() {
		$domxml = new DOMDocument('1.0', 'UTF-8');
		$domxml->preserveWhiteSpace = false;
		$domxml->formatOutput = true;
		$config = $domxml->appendChild(new DOMElement('LiveVoting'));

		$xml_info = $config->appendChild(new DOMElement('info'));
		$xml_info->appendChild(new DOMElement('plugin_version', $this->pl->getVersion()));
		$xml_info->appendChild(new DOMElement('plugin_db_version', $this->pl->getDBVersion()));

		// xoctConf
		$xml_votings = $config->appendChild(new DOMElement('votings'));

		/**
		 * @var $xlvoVoting xlvoVoting
		 * @var $xlvoOption xlvoOption
		 */
		foreach (xlvoVoting::where(array( 'obj_id' => $this->getObjId() ))->get() as $xlvoVoting) {
			$xml_voting = $xml_votings->appendChild(new DOMElement('voting'));
			$xml_voting->appendChild(new DOMElement('title'))->appendChild(new DOMCdataSection($xlvoVoting->getTitle()));
			$xml_voting->appendChild(new DOMElement('description'))->appendChild(new DOMCdataSection($xlvoVoting->getDescription()));
			$xml_voting->appendChild(new DOMElement('question'))->appendChild(new DOMCdataSection($xlvoVoting->getQuestion()));
			$xml_voting->appendChild(new DOMElement('voting_type'))->appendChild(new DOMCdataSection($xlvoVoting->getVotingType()));
			$xml_voting->appendChild(new DOMElement('multi_selection'))->appendChild(new DOMCdataSection($xlvoVoting->isMultiSelection()));
			$xml_voting->appendChild(new DOMElement('colors'))->appendChild(new DOMCdataSection($xlvoVoting->isColors()));
			$xml_voting->appendChild(new DOMElement('multi_free_input'))->appendChild(new DOMCdataSection($xlvoVoting->isMultiFreeInput()));
			$xml_voting->appendChild(new DOMElement('voting_status'))->appendChild(new DOMCdataSection($xlvoVoting->getVotingStatus()));
			$xml_voting->appendChild(new DOMElement('position'))->appendChild(new DOMCdataSection($xlvoVoting->getPosition()));
			$xml_voting->appendChild(new DOMElement('columns'))->appendChild(new DOMCdataSection($xlvoVoting->getColumns()));

			$xml_options = $xml_voting->appendChild(new DOMElement('options'));
			foreach ($xlvoVoting->getVotingOptions() as $xlvoOption) {
				$xml_option = $xml_options->appendChild(new DOMElement('option'));
				$xml_option->appendChild(new DOMElement('text'))->appendChild(new DOMCdataSection($xlvoOption->getText()));
				$xml_option->appendChild(new DOMElement('type'))->appendChild(new DOMCdataSection($xlvoOption->getType()));
				$xml_option->appendChild(new DOMElement('status'))->appendChild(new DOMCdataSection($xlvoOption->getStatus()));
				$xml_option->appendChild(new DOMElement('position'))->appendChild(new DOMCdataSection($xlvoOption->getPosition()));
				$xml_option->appendChild(new DOMElement('correct_position'))->appendChild(new DOMCdataSection($xlvoOption->getCorrectPosition()));
			}
		}

		file_put_contents('/tmp/votings.xml', $domxml->saveXML());
		ob_end_clean();
		ilUtil::deliverFile('/tmp/votings.xml', 'votings.xml');
		unlink('/tmp/votings.xml');
	}


	protected function import() {
		$domxml = new DOMDocument('1.0', 'UTF-8');
		$domxml->loadXML(file_get_contents($_FILES['xlvo_import']['tmp_name']));

		/**
		 * @var $node DOMElement
		 */
		$xoct_confs = $domxml->getElementsByTagName('voting');
		foreach ($xoct_confs as $node) {
			$title = $node->getElementsByTagName('title')->item(0)->nodeValue;
			$description = $node->getElementsByTagName('description')->item(0)->nodeValue;
			$question = $node->getElementsByTagName('question')->item(0)->nodeValue;
			$voting_type = $node->getElementsByTagName('voting_type')->item(0)->nodeValue;
			$multi_selection = $node->getElementsByTagName('multi_selection')->item(0)->nodeValue;
			$colors = $node->getElementsByTagName('colors')->item(0)->nodeValue;
			$multi_free_input = $node->getElementsByTagName('multi_free_input')->item(0)->nodeValue;
			$voting_status = $node->getElementsByTagName('voting_status')->item(0)->nodeValue;
			$position = $node->getElementsByTagName('position')->item(0)->nodeValue;
			$columns = $node->getElementsByTagName('columns')->item(0)->nodeValue;

			$xlvoVoting = new xlvoVoting();
			$xlvoVoting->setObjId($this->getObjId());
			$xlvoVoting->setTitle($title);
			$xlvoVoting->setDescription($description);
			$xlvoVoting->setQuestion($question);
			$xlvoVoting->setVotingType($voting_type);
			$xlvoVoting->setMultiSelection($multi_selection);
			$xlvoVoting->setColors($colors);
			$xlvoVoting->setMultiFreeInput($multi_free_input);
			$xlvoVoting->setVotingStatus($voting_status);
			$xlvoVoting->setPosition($position);
			$xlvoVoting->setColumns($columns ? $columns : 2);
			$xlvoVoting->create();

			$options = $node->getElementsByTagName('option');
			$xlvoOptions = array();
			/**
			 * @var $option DOMElement
			 */
			foreach ($options as $option) {
				$text = $option->getElementsByTagName('text')->item(0)->nodeValue;
				$type = $option->getElementsByTagName('type')->item(0)->nodeValue;
				$status = $option->getElementsByTagName('status')->item(0)->nodeValue;
				$position = $option->getElementsByTagName('position')->item(0)->nodeValue;
				$correct_position = $option->getElementsByTagName('correct_position')->item(0)->nodeValue;

				$xlvoOption = new xlvoOption();
				$xlvoOption->setText($text);
				$xlvoOption->setType($type);
				$xlvoOption->setStatus($status);
				$xlvoOption->setPosition($position);
				$xlvoOption->setCorrectPosition($correct_position);
				$xlvoOption->setVotingId($xlvoVoting->getId());
				$xlvoOption->create();

				$xlvoOptions[] = $xlvoOption;
			}
			$xlvoVoting->setVotingOptions($xlvoOptions);
			$xlvoVoting->renegerateOptionSorting();
		}
		$this->cancel();
	}
}