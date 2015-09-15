<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoMultiLineInputGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManager.php');

class xlvoSingleVoteVotingFormGUI extends xlvoVotingFormGUI {

	/**
	 * @var  xlvoVoting
	 */
	protected $voting;
	/**
	 * @var xlvoOption
	 */
	protected $options;
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
	 * @var bool
	 */
	protected $has_existing_votes;


	/**
	 * @param            $parent_gui
	 * @param xlvoVoting $xlvoVoting
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
		$this->is_new = ($this->voting->getVotingStatus() == xlvoVoting::STAT_INCOMPLETE);
		$this->options = array();
		$this->voting_manager = new xlvoVotingManager();
		$this->has_existing_votes = (xlvoVote::where(array( 'voting_id' => $this->voting->getId() ))->count() > 0);

		global $tpl;
		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/confirm_delete.js');

		$this->initForm();
	}


	protected function initForm() {
		$this->setTarget('_top');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initButtons();

		$cb = new ilCheckboxInputGUI($this->pl->txt('multi_selection'), 'multi_selection');
		$cb->setInfo($this->pl->txt('info_singlevote_multi_selection'));
		$this->addItem($cb);
		$cb = new ilCheckboxInputGUI($this->pl->txt('colors'), 'colors');
		$cb->setInfo($this->pl->txt('info_singlevote_colors'));
		$this->addItem($cb);

		if (! $this->has_existing_votes) {
			$mli = new xlvoMultiLineInputGUI($this->pl->txt('options'), 'options');
			$mli->setInfo($this->pl->txt('info_singlevote_options'));
			$te = new ilTextInputGUI($this->pl->txt('text'), 'text');
			$mli->addInput($te);
			$this->addItem($mli);
		} else {
			// TODO add info
		}
	}


	public function fillForm() {
		if ($this->voting->getObjId() == $this->parent_gui->getObjId()) {

			if (! $this->has_existing_votes) {
				$this->options = xlvoOption::where(array(
					'voting_id' => $this->voting->getId(),
					'status' => xlvoOption::STAT_ACTIVE
				))->getArray();
			}

			$array = array(
				'multi_selection' => $this->voting->isMultiSelection(),
				'colors' => $this->voting->isColors(),
				'options' => $this->options,
			);
			$this->setValuesByArray($array);
			if ($this->voting->getVotingStatus() == xlvoVoting::STAT_INCOMPLETE) {
				ilUtil::sendInfo($this->pl->txt('voting_not_complete'), false);
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

		$this->voting->setMultiSelection($this->getInput('multi_selection'));
		$this->voting->setColors($this->getInput('colors'));
		$opts = $this->getInput('options');

		$this->options = xlvoOption::where(array(
			'voting_id' => $this->voting->getId(),
			'status' => xlvoOption::STAT_ACTIVE
		))->getArray();

		if (! $this->has_existing_votes) {
			$arr_existing_ids = array();
			$arr_existing_texts = array();
			$arr_opts_ids = array();

			foreach ($this->options as $opt) {
				$arr_existing_ids[$opt['id']] = $opt['id'];
				$arr_existing_texts[$opt['id']] = $opt['text'];
			}

			foreach ($opts as $opt) {
				$arr_opts_ids[array_search($opt, $opts)] = array_search($opt, $opts);
			}

			$this->options = array();

			foreach ($opts as $opt) {
				$option = new xlvoOption();
				$opt_id = array_search($opt, $opts);
				if ($opt_id == $arr_existing_ids[$opt_id]) {
					$option->setId($opt_id);
				}
				$option->setText($opt['text']);
				$option->setType($this->voting->getVotingType());
				$option->setVotingId($this->voting->getId());
				$option->setStatus(xlvoOption::STAT_ACTIVE);

				array_push($this->options, $option);
			}

			foreach ($arr_existing_ids as $id) {
				if ($arr_opts_ids[$id] == NULL) {
					var_dump($id);
					$option = new xlvoOption();
					$option->setId($id);
					$option->setVotingId($this->voting->getId());
					$option->setStatus(xlvoOption::STAT_INACTIVE);
					array_push($this->options, $option);
				}
			}
		} else {
			$this->options = array();
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

			if (! $this->has_existing_votes) {
				foreach ($this->options as $option) {
					if ($this->voting->getId() == $option->getVotingId()) {
						if (! xlvoOption::where(array( 'id' => $option->getId() ))->hasSets()) {
							$option->create();
						} else {
							if ($option->getStatus() == xlvoOption::STAT_ACTIVE) {
								$option->update();
							} else {
								$this->voting_manager->deleteVotesOfOption($option->getId());
								$option->delete();
							}
						}
					} else {
						ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
						$this->ctrl->redirect($this->parent_gui, xlvoVotingGUI::CMD_STANDARD);
					}
				}
			}

			if (xlvoVoting::where(array( 'id' => $this->voting->getId() ))->hasSets()) {
				if ($this->voting->getVotingStatus() == xlvoVoting::STAT_INCOMPLETE) {
					$this->voting->setVotingStatus(xlvoVoting::STAT_ACTIVE);
				}
				$this->voting->update();
			}
		} else {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
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