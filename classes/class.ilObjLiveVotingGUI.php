<?php

require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('./Services/Repository/classes/class.ilObjectPluginGUI.php');
require_once('./Services/AccessControl/classes/class.ilPermissionGUI.php');
require_once('./Services/InfoScreen/classes/class.ilInfoScreenGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilObjLiveVotingAccess.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingPlugin.php');

/**
 * Class ilObjLiveVotingGUI
 *
 * @ilCtrl_isCalledBy ilObjLiveVotingGUI: ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 * @ilCtrl_Calls      ilObjLiveVotingGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI, xlvoVotingGUI
 *
 */
class ilObjLiveVotingGUI extends ilObjectPluginGUI {

	const XLVO = 'xlvo';
	const CMD_STANDARD = 'showContent';
	const CMD_EDIT = 'editProperties';
	const CMD_UPDATE = 'updateProperties';
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
	 * @var ilObjLiveVotingAccess
	 */
	protected $access;
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;
	/**
	 * @var xlvoVotingConfig
	 */
	protected $config;
	/**
	 * @var ilUser
	 */
	protected $usr;


	protected function afterConstructor() {
		global $tpl, $ilCtrl, $ilTabs, $ilUser;

		/**
		 * @var $tpl    ilTemplate
		 * @var $ilCtrl ilCtrl
		 * @var $ilTabs ilTabsGUI
		 * @var $ilUser ilUser
		 */
		$this->tpl = $tpl;
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->usr = $ilUser;
		$this->access = new ilObjLiveVotingAccess();
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->config = xlvoVotingConfig::find($this->obj_id);
	}


	/**
	 * @return string
	 */
	public final function getType() {
		return self::XLVO;
	}


	public function executeCommand() {
		global $ilNavigationHistory;

		// get standard template (includes main menu and general layout)
		$this->tpl->getStandardTemplate();
		$this->setTitleAndDescription();
		// set title
		if (! $this->getCreationMode()) {
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

		$next_class = $this->ctrl->getNextClass($this);
		$cmd = $this->ctrl->getCmd();

		switch ($next_class) {
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

			case 'illearningprogressgui':
				$this->tabs->setTabActive('learning_progress');
				include_once './Services/Tracking/classes/class.ilLearningProgressGUI.php';
				$new_gui = new ilLearningProgressGUI(ilLearningProgressGUI::LP_CONTEXT_REPOSITORY, $this->object->getRefId(), $_GET['user_id'] ? $_GET['user_id'] : $GLOBALS['ilUser']->getId());
				$this->ctrl->forwardCommand($new_gui);
				break;

			default:
				if (strtolower($_GET['baseClass']) == 'iladministrationgui') {
					$this->viewObject();

					return;
				}
				if (! $cmd) {
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

		if (! $this->getCreationMode()) {
			$this->tpl->show();
		}
	}


	protected function performCommand() {
		$cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
		switch ($cmd) {
			case self::CMD_STANDARD:
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
		return self::CMD_STANDARD;
	}


	/**
	 * @return string
	 */
	public function getStandardCmd() {
		return self::CMD_STANDARD;
	}


	protected function setTabs() {
		$this->addInfoTab();
		if ($this->access->hasWriteAccess()) {
			$this->tabs->addTab(self::CMD_EDIT, $this->pl->txt('edit_properties'), $this->ctrl->getLinkTarget($this, self::CMD_EDIT));
		}
		$this->tabs->addTab(self::CMD_STANDARD, $this->pl->txt('standard'), $this->ctrl->getLinkTarget($this, self::CMD_STANDARD));
		parent::setTabs();

		return true;
	}


	public function showContent() {
		$this->tabs->setTabActive(self::CMD_STANDARD);
		$this->tpl->setContent('hello');
	}


	public function editProperties() {
		if (! $this->access->hasWriteAccess()) {
			ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$this->tabs->activateTab(self::CMD_EDIT);
			$this->initPropertiesForm();
			$this->fillPropertiesForm();
			$this->tpl->setContent($this->form->getHTML());
		}
	}


	public function initPropertiesForm() {
		$this->form = new ilPropertyFormGUI();
		$this->form->setTitle($this->pl->txt('edit_properties'));

		$ti = new ilTextInputGUI($this->pl->txt('title'), 'title');
		$ti->setRequired(true);
		$this->form->addItem($ti);
		$ta = new ilTextAreaInputGUI($this->pl->txt('description'), 'description');
		$this->form->addItem($ta);
		$cb = new ilCheckboxInputGUI($this->pl->txt('online'), 'online');
		$this->form->addItem($cb);
		$cb = new ilCheckboxInputGUI($this->pl->txt('anonymous'), 'anonymous');
		$this->form->addItem($cb);
		$cb = new ilCheckboxInputGUI($this->pl->txt('terminable'), 'terminable');
		//		$cb->setValue(1);
		$this->form->addItem($cb);

		require_once('./Services/Form/classes/class.ilDateDurationInputGUI.php');
		$te = new ilDateDurationInputGUI($this->pl->txt("terminable_select"), "terminable_select");
		$te->setShowTime(true);
		$te->setStartText($this->pl->txt("terminable_select_start_time"));
		$te->setEndText($this->pl->txt("terminable_select_end_time"));
		$te->setMinuteStepSize(1);
		if ($this->config->isTerminable()) {
			if (! $this->config->getStartDate() == NULL) {
				$te->setStart(new ilDateTime($this->config->getStartDate(), IL_CAL_DATETIME, $this->usr->getTimeZone()));
				$te->setEnd(new ilDateTime($this->config->getEndDate(), IL_CAL_DATETIME, $this->usr->getTimeZone()));
			} else {
				$te->setStart(new ilDateTime(date('Y-m-d H:i:s'), IL_CAL_DATETIME, $this->usr->getTimeZone()));
				$te->setEnd(new ilDateTime(date('Y-m-d H:i:s'), IL_CAL_DATETIME, $this->usr->getTimeZone()));
			}
		}
		$cb->addSubItem($te);

		$this->form->addCommandButton('updateProperties', $this->pl->txt('save'));

		$this->form->setFormAction($this->ctrl->getFormAction($this));
	}


	function fillPropertiesForm() {

		$values['title'] = $this->object->getTitle();
		$values['description'] = $this->object->getDescription();
		$values['online'] = $this->config->isObjOnline();
		$values['anonymous'] = $this->config->isAnonymous();
		$values['terminable'] = $this->config->isTerminable();

		$this->form->setValuesByArray($values);
	}


	public function updateProperties() {
		$this->initPropertiesForm();

		if ($this->form->checkInput()) {
			$this->object->setTitle($this->form->getInput('title'));
			$this->object->setDescription($this->form->getInput('description'));
			$this->object->update();
			$this->config->setObjOnline($this->form->getInput('online'));
			$this->config->setAnonymous($this->form->getInput('anonymous'));
			$terminable = $this->form->getInput('terminable');
			$this->config->setTerminable($terminable);
			$terminable_select = $this->form->getInput("terminable_select");
			if ($terminable) {
				$this->config->setStartDate($this->getDateTimeFromArray($terminable_select['start']));
				$this->config->setEndDate($this->getDateTimeFromArray($terminable_select['end']));
			} else {
				$this->config->setStartDate(NULL);
				$this->config->setEndDate(NULL);
			}

			$this->config->update();
			ilUtil::sendSuccess($this->pl->txt('system_account_msg_success'), true);
			$this->ctrl->redirect($this, self::CMD_EDIT);
		}

		$this->form->setValuesByPost();
		$this->tpl->setContent($this->form->getHtml());
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
}