<?php

require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('./Services/Repository/classes/class.ilObjectPluginGUI.php');
require_once('./Services/AccessControl/classes/class.ilPermissionGUI.php');
require_once('./Services/InfoScreen/classes/class.ilInfoScreenGUI.php');
require_once('./Services/UIComponent/Button/classes/class.ilLinkButton.php');
require_once('./Services/Form/classes/class.ilDateDurationInputGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVotingGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voter/class.xlvoVoterGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/class.xlvoPlayerGUI.php');

/**
 * Class ilObjLiveVotingGUI
 *
 * @ilCtrl_isCalledBy ilObjLiveVotingGUI: ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 * @ilCtrl_Calls      ilObjLiveVotingGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilObjLiveVotingGUI: xlvoVoterGUI, xlvoPlayerGUI, xlvoVotingGUI
 *
 * @author            Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 */
class ilObjLiveVotingGUI extends ilObjectPluginGUI {

	const XLVO = 'xlvo';
	const CMD_STANDARD = 'showContent';
	const CMD_AFTER_CREATION = 'showContentAfterCreation';
	const CMD_SHOW_CONTENT = 'showContent';
	const CMD_EDIT = 'editProperties';
	const CMD_UPDATE = 'updateProperties';
	const TAB_EDIT = 'tab_edit';
	const SUBTAB_SHOW = 'subtab_show';
	const SUBTAB_EDIT = 'subtab_edit';
	const TAB_CONTENT = 'tab_content';
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


	protected function afterConstructor() {
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
	}


	/**
	 * @return string
	 */
	public final function getType() {
		return self::XLVO;
	}


	protected function initHeaderAndLocator() {
		global $ilNavigationHistory;

		// get standard template (includes main menu and general layout)
		$this->tpl->getStandardTemplate();
		$this->setTitleAndDescription();
		// set title
		if (!$this->getCreationMode()) {
			$this->tpl->setTitle($this->object->getTitle());
			$this->tpl->setTitleIcon(ilObject::_getIcon($this->object->getId()));

			// set tabs
			if (strtolower($_GET['baseClass']) != 'iladministrationgui') {
				$this->setTabs();
				$this->setLocator();
			} else {
				$this->addAdminLocatorItems();
				$this->tpl->setLocator();
				$this->setAdminTabs();
			}

			global $ilAccess;
			// add entry to navigation history
			if ($ilAccess->checkAccess('read', '', $_GET['ref_id'])) {
				$ilNavigationHistory->addItem($_GET['ref_id'], $this->ctrl->getLinkTarget($this, $this->getStandardCmd()), $this->getType());
			}
		} else {
			// show info of parent
			$this->tpl->setTitle(ilObject::_lookupTitle(ilObject::_lookupObjId($_GET['ref_id'])));
			$this->tpl->setTitleIcon(ilObject::_getIcon(ilObject::_lookupObjId($_GET['ref_id']), 'big'), $this->pl->txt('obj_'
				. ilObject::_lookupType($_GET['ref_id'], true)));
			$this->setLocator();
		}
	}


	/**
	 * @throws ilCtrlException
	 */
	public function executeCommand() {
		$this->initHeaderAndLocator();

		$next_class = $this->ctrl->getNextClass($this);
		$cmd = $this->ctrl->getCmd();

		switch ($next_class) {
			case 'xlvovotinggui':
				$xlvoVotingGUI = new xlvoVotingGUI();
				$this->setSubTabs(self::TAB_CONTENT, self::SUBTAB_EDIT);
				$this->ctrl->forwardCommand($xlvoVotingGUI);
				break;

			case 'xlvovotergui':
				$xlvoVoterGUI = new xlvoVoterGUI();
				$this->ctrl->forwardCommand($xlvoVoterGUI);
				break;

			case 'xlvoplayergui':
				$xlvoPlayerGUI = new xlvoPlayerGUI();
				$this->setSubTabs(self::TAB_CONTENT, self::SUBTAB_SHOW);
				$this->ctrl->forwardCommand($xlvoPlayerGUI);
				break;

			case 'ilinfoscreengui':
				$this->checkPermission('visible');
				$this->infoScreen();    // forwards command
				break;

			case 'ilpermissiongui':
				include_once('Services/AccessControl/classes/class.ilPermissionGUI.php');
				$perm_gui = new ilPermissionGUI($this);
				$this->tabs->setTabActive('id_permissions');
				$this->ctrl->forwardCommand($perm_gui);
				break;

			case 'ilobjectcopygui':
				include_once './Services/Object/classes/class.ilObjectCopyGUI.php';
				$cp = new ilObjectCopyGUI($this);
				$cp->setType($this->getType());
				$this->ctrl->forwardCommand($cp);
				break;

			//			case 'illearningprogressgui':
			//				$this->tabs->setTabActive('learning_progress');
			//				include_once './Services/Tracking/classes/class.ilLearningProgressGUI.php';
			//				$new_gui = new ilLearningProgressGUI(ilLearningProgressGUI::LP_CONTEXT_REPOSITORY, $this->object->getRefId(), $_GET['user_id'] ? $_GET['user_id'] : $GLOBALS['ilUser']->getId());
			//				$this->ctrl->forwardCommand($new_gui);
			//				break;

			default:
				if (strtolower($_GET['baseClass']) == 'iladministrationgui') {
					$this->viewObject();

					return;
				}
				if (!$cmd) {
					$cmd = $this->getStandardCmd();
				}
				if ($cmd == 'infoScreen') {
					$this->ctrl->setCmd('showSummary');
					$this->ctrl->setCmdClass('ilinfoscreengui');
					$this->infoScreen();
				} else {
					if ($this->getCreationMode()) {
						$this->$cmd();
					} else {
						$this->performCommand();
					}
				}
				break;
		}

		if (!$this->getCreationMode()) {
			$this->tpl->show();
		}
	}


	protected function performCommand() {
		$cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
		switch ($cmd) {
			case self::CMD_STANDARD:
			case self::CMD_SHOW_CONTENT:
			case self::CMD_AFTER_CREATION:
			case self::CMD_EDIT:
			case self::CMD_UPDATE:
				$this->{$cmd}();
				break;
		}
	}


	/**
	 * @return string
	 */
	public function getAfterCreationCmd() {
		return self::CMD_AFTER_CREATION;
	}


	/**
	 * @return string
	 */
	public function getStandardCmd() {
		return self::CMD_STANDARD;
	}


	protected function setTabs() {
		$this->tabs->addTab(self::TAB_CONTENT, $this->pl->txt(self::TAB_CONTENT), $this->ctrl->getLinkTarget(new xlvoPlayerGUI(), xlvoPlayerGUI::CMD_START_OF_VOTING_SCREEN));
		$this->addInfoTab();
		if ($this->access->hasWriteAccess()) {
			$this->tabs->addTab(self::TAB_EDIT, $this->pl->txt(self::TAB_EDIT), $this->ctrl->getLinkTarget($this, self::CMD_EDIT));
		}
		parent::setTabs();

		return true;
	}


	/**
	 * @param $tab
	 */
	protected function setSubTabs($tab, $active_subtab = null) {
		$this->tabs->setTabActive($tab);
		switch ($tab) {
			case self::TAB_CONTENT:
				$this->tabs->addSubTab(self::SUBTAB_SHOW, $this->pl->txt(self::SUBTAB_SHOW), $this->ctrl->getLinkTarget(new xlvoPlayerGUI(), xlvoPlayerGUI::CMD_START_OF_VOTING_SCREEN));
				if ($this->access->hasWriteAccess()) {
					$this->tabs->addSubTab(self::SUBTAB_EDIT, $this->pl->txt(self::SUBTAB_EDIT), $this->ctrl->getLinkTarget(new xlvoVotingGUI(), xlvoVotingGUI::CMD_STANDARD));
				}
				break;
		}
		if ($active_subtab) {
			$this->tabs->setSubTabActive($active_subtab);
		}
	}


	public function showContent() {
		$this->ctrl->redirect(new xlvoPlayerGUI(), xlvoPlayerGUI::CMD_START_OF_VOTING_SCREEN);
	}


	public function showContentAfterCreation() {
		$this->ctrl->redirect(new xlvoVotingGUI(), xlvoVotingGUI::CMD_STANDARD);
	}


	public function editProperties() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('obj_permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$this->tabs->setTabActive(self::TAB_EDIT);
			$this->initPropertiesForm();
			$this->fillPropertiesForm();
			$this->tpl->setContent($this->form->getHTML());
		}
	}


	protected function initPropertiesForm() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure(ilLiveVotingPlugin::getInstance()->txt('obj_permission_denied'), true);
		} else {
			$this->form = new ilPropertyFormGUI();
			$this->form->setTitle($this->pl->txt('obj_edit_properties'));

			$ti = new ilTextInputGUI($this->pl->txt('obj_title'), 'title');
			$ti->setRequired(true);
			$this->form->addItem($ti);
			$ta = new ilTextAreaInputGUI($this->pl->txt('obj_description'), 'description');
			$this->form->addItem($ta);
			$cb = new ilCheckboxInputGUI($this->pl->txt('obj_online'), 'online');
			$cb->setInfo($this->pl->txt('obj_info_online'));
			$this->form->addItem($cb);
			$cb = new ilCheckboxInputGUI($this->pl->txt('obj_anonymous'), 'anonymous');
			$cb->setInfo($this->pl->txt('obj_info_anonymous'));
			$this->form->addItem($cb);

			$cb = new ilCheckboxInputGUI($this->pl->txt('obj_reuse_status'), 'reuse_status');
			$cb->setInfo($this->pl->txt('obj_info_reuse_status'));
			$this->form->addItem($cb);

			$cb = new ilCheckboxInputGUI($this->pl->txt('obj_terminable'), 'terminable');
			$cb->setInfo($this->pl->txt('obj_info_terminable'));
			$this->form->addItem($cb);

			$te = new ilDateDurationInputGUI($this->pl->txt("obj_terminable_select"), "terminable_select");
			$te->setShowTime(true);
			$te->setStartText($this->pl->txt('obj_terminable_select_start_time'));
			$te->setEndText($this->pl->txt('obj_terminable_select_end_time'));
			$te->setMinuteStepSize(1);
			/**
			 * @var xlvoVotingConfig $config
			 */
			$config = xlvoVotingConfig::find($this->obj_id);
			if ($config->isTerminable()) {
				if (!$config->getStartDate() == NULL) {
					$te->setStart(new ilDateTime($config->getStartDate(), IL_CAL_DATETIME, $this->usr->getTimeZone()));
					$te->setEnd(new ilDateTime($config->getEndDate(), IL_CAL_DATETIME, $this->usr->getTimeZone()));
				} else {
					$te->setStart(new ilDateTime(date('Y-m-d H:i:s'), IL_CAL_DATETIME, $this->usr->getTimeZone()));
					$te->setEnd(new ilDateTime(date('Y-m-d H:i:s'), IL_CAL_DATETIME, $this->usr->getTimeZone()));
				}
			}
			$cb->addSubItem($te);

			$this->form->addCommandButton('updateProperties', $this->pl->txt('obj_save'));

			$this->form->setFormAction($this->ctrl->getFormAction($this));
		}
	}


	protected function fillPropertiesForm() {

		/**
		 * @var $config xlvoVotingConfig
		 */
		$config = xlvoVotingConfig::find($this->obj_id);

		$values['title'] = $this->object->getTitle();
		$values['description'] = $this->object->getDescription();
		$values['online'] = $config->isObjOnline();
		$values['anonymous'] = $config->isAnonymous();
		$values['terminable'] = $config->isTerminable();
		$values['reuse_status'] = $config->isReuseStatus();

		$this->form->setValuesByArray($values);
	}


	public function updateProperties() {
		if (!$this->access->hasWriteAccess()) {
			ilUtil::sendFailure(ilLiveVotingPlugin::getInstance()->txt('obj_permission_denied_write'), true);
		} else {
			$this->tabs->setTabActive(self::TAB_EDIT);
			$this->initPropertiesForm();

			if ($this->form->checkInput()) {
				$this->object->setTitle($this->form->getInput('title'));
				$this->object->setDescription($this->form->getInput('description'));
				$this->object->update();

				/**
				 * @var xlvoVotingConfig $config
				 */
				$config = xlvoVotingConfig::find($this->obj_id);
				$config->setObjOnline($this->form->getInput('online'));
				$config->setAnonymous($this->form->getInput('anonymous'));
				$config->setReuseStatus($this->form->getInput('reuse_status'));
				$terminable = $this->form->getInput('terminable');
				$config->setTerminable($terminable);
				$terminable_select = $this->form->getInput("terminable_select");
				if ($terminable) {
					$config->setStartDate($this->getDateTimeFromArray($terminable_select['start']));
					$config->setEndDate($this->getDateTimeFromArray($terminable_select['end']));
				} else {
					$config->setStartDate(NULL);
					$config->setEndDate(NULL);
				}
				$config->update();
				ilUtil::sendSuccess($this->pl->txt('obj_msg_properties_form_saved'), true);
				$this->ctrl->redirect($this, self::CMD_EDIT);
			}

			$this->form->setValuesByPost();
			$this->tpl->setContent($this->form->getHtml());
		}
	}


	/**
	 * getDateTimeFromArray
	 *
	 * @access protected
	 *
	 * @param  $a_field array
	 *
	 * @return int
	 */
	protected function getDateTimeFromArray($a_field) {
		require_once('./Services/Calendar/classes/class.ilDateTime.php');
		$dt['year'] = (int)$a_field['date']['y'];
		$dt['mon'] = (int)$a_field['date']['m'];
		$dt['mday'] = (int)$a_field['date']['d'];
		$dt['hours'] = (int)$a_field['time']['h'];
		$dt['minutes'] = (int)$a_field['time']['m'];
		$dt['seconds'] = (int)$a_field['time']['s'];
		$date = new ilDateTime($dt, IL_CAL_FKT_GETDATE, $this->usr->getTimeZone());

		$date->setDate($date, 'yyyy-mm-dd h:m:s');

		return $date->get(IL_CAL_DATETIME, $this->usr->getTimeZone());
	}


	/**
	 * Goto redirection
	 */
	public static function _goto($a_target) {
		if (preg_match("/[\\d]*_pin_([\\w]*)/", $a_target[0], $matches)) {
			global $ilCtrl;
			/**
			 * @var ilCtrl $ilCtrl
			 */
			xlvoInitialisation::setCookiePIN($matches[1], true);
			//			ilUtil::redirect('Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php?pin='.$matches[1]);
			$ilCtrl->initBaseClass('ilUIPluginRouterGUI');
			$ilCtrl->setTargetScript('Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/xlvoILIAS.php');
			$ilCtrl->redirectByClass(array(
				'ilUIPluginRouterGUI',
				'xlvoVoterGUI'
			));
		}

		parent::_goto($a_target);
	}
}