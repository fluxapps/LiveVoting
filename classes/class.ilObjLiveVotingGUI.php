<?php

require_once __DIR__ . '/../vendor/autoload.php';
use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\cookie\CookieManager;
use LiveVoting\Context\xlvoContext;
use LiveVoting\Context\xlvoInitialisation;
use LiveVoting\Voting\xlvoVotingManager2;

require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('./Services/Repository/classes/class.ilObjectPluginGUI.php');
require_once('./Services/AccessControl/classes/class.ilPermissionGUI.php');
require_once('./Services/InfoScreen/classes/class.ilInfoScreenGUI.php');
require_once('./Services/UIComponent/Button/classes/class.ilLinkButton.php');
require_once('./Services/Form/classes/class.ilDateDurationInputGUI.php');

/**
 * Class ilObjLiveVotingGUI
 *
 * @ilCtrl_isCalledBy ilObjLiveVotingGUI: ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 * @ilCtrl_Calls      ilObjLiveVotingGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilObjLiveVotingGUI: xlvoVoterGUI, xlvoPlayerGUI, xlvoPlayer2GUI, xlvoVotingGUI, xlvoResultsGUI
 *
 * @author            Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 */
class ilObjLiveVotingGUI extends \ilObjectPluginGUI {

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
	const TAB_RESULTS = 'tab_results';
	const F_TITLE = 'title';
	const F_DESCRIPTION = 'description';
	/**
	 * @var \ilTemplate
	 */
	public $tpl;
	/**
	 * @var \ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var \ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var \ilToolbarGUI
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
	 * @var \ilObjUser
	 */
	protected $usr;


	protected function afterConstructor() {
		global $tpl, $ilCtrl, $ilTabs, $ilUser, $ilToolbar;

		/**
		 * @var $tpl       \ilTemplate
		 * @var $ilCtrl    \ilCtrl
		 * @var $ilTabs    \ilTabsGUI
		 * @var $ilUser    \ilObjUser
		 * @var $ilToolbar \ilToolbarGUI
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
			$this->tpl->setTitleIcon(\ilObject::_getIcon($this->object->getId()));
			$this->ctrl->saveParameterByClass('xlvoresultsgui', 'round_id');

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
			$this->tpl->setTitle(\ilObject::_lookupTitle(\ilObject::_lookupObjId($_GET['ref_id'])));
			$this->tpl->setTitleIcon(\ilObject::_getIcon(\ilObject::_lookupObjId($_GET['ref_id']), 'big'), $this->pl->txt('obj_'
			                                                                                                            . \ilObject::_lookupType($_GET['ref_id'], true)));
			$this->setLocator();
		}
	}


	/**
	 * @throws \ilCtrlException
	 */
	public function executeCommand() {
		$this->initHeaderAndLocator();

		$this->tpl->setPermanentLink('xlvo', $_GET['ref_id']);

		$next_class = $this->ctrl->getNextClass($this);
		$cmd = $this->ctrl->getCmd();

		if (!$this->access->hasWriteAccess()) {
			$xlvoVotingManager2 = xlvoVotingManager2::getInstanceFromObjId($this->obj_id);
			global $ilCtrl;
			/**
			 * @var \ilCtrl $ilCtrl
			 */
			xlvoInitialisation::setCookiePIN($xlvoVotingManager2->getVotingConfig()->getPin(), true);
            CookieManager::setContext(xlvoContext::CONTEXT_ILIAS);

			$ilCtrl->initBaseClass('ilUIPluginRouterGUI');
			$ilCtrl->setTargetScript(xlvoConf::getFullApiURL());
			$ilCtrl->redirectByClass(array(
				'ilUIPluginRouterGUI',
				'xlvoVoter2GUI',
			));
		}

		switch ($next_class) {
			case 'xlvovotinggui':
				$xlvoVotingGUI = new xlvoVotingGUI();
				$this->setSubTabs(self::TAB_CONTENT, self::SUBTAB_EDIT);
				$this->ctrl->forwardCommand($xlvoVotingGUI);
				break;

			case 'xlvoresultsgui':
				$xlvoResultsGUI = new xlvoResultsGUI($this->obj_id);
				$this->tabs->setTabActive(self::TAB_RESULTS);
				$this->ctrl->forwardCommand($xlvoResultsGUI);
				break;

			case 'xlvoplayergui':
				$xlvoPlayerGUI = new xlvoPlayerGUI();
				$this->setSubTabs(self::TAB_CONTENT, self::SUBTAB_SHOW);
				$this->ctrl->forwardCommand($xlvoPlayerGUI);
				break;

			case "ilinfoscreengui":
				$this->checkPermission("visible");
				$this->infoScreen();    // forwards command
				break;

			case 'ilpermissiongui':
				include_once("Services/AccessControl/classes/class.ilPermissionGUI.php");
				$perm_gui = new \ilPermissionGUI($this);
				$this->tabs->setTabActive("perm_settings");
				$ret = $this->ctrl->forwardCommand($perm_gui);
				break;

			case 'ilobjectcopygui':
				include_once './Services/Object/classes/class.ilObjectCopyGUI.php';
				$cp = new \ilObjectCopyGUI($this);
				$cp->setType($this->getType());
				$this->ctrl->forwardCommand($cp);
				break;

			case 'illearningprogressgui':
				$this->tabs->setTabActive("learning_progress");
				include_once './Services/Tracking/classes/class.ilLearningProgressGUI.php';
				$new_gui = new \ilLearningProgressGUI(\ilLearningProgressGUI::LP_CONTEXT_REPOSITORY, $this->object->getRefId(), $_GET['user_id'] ? $_GET['user_id'] : $GLOBALS['ilUser']->getId());
				$this->ctrl->forwardCommand($new_gui);
				break;
			case 'ilcommonactiondispatchergui':
				include_once("Services/Object/classes/class.ilCommonActionDispatcherGUI.php");
				$gui = \ilCommonActionDispatcherGUI::getInstanceFromAjaxCall();
				$this->ctrl->forwardCommand($gui);
				break;

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
		$this->tabs->addTab(self::TAB_CONTENT, $this->pl->txt(self::TAB_CONTENT), $this->ctrl->getLinkTargetByClass('xlvoplayergui', xlvoPlayerGUI::CMD_STANDARD));
		$this->addInfoTab();
		if ($this->access->hasWriteAccess()) {
			$this->tabs->addTab(self::TAB_EDIT, $this->pl->txt(self::TAB_EDIT), $this->ctrl->getLinkTargetByClass('ilobjlivevotinggui', self::CMD_EDIT));
			$this->tabs->addTab(self::TAB_RESULTS, $this->pl->txt(self::TAB_RESULTS), $this->ctrl->getLinkTargetByClass('xlvoResultsGUI', xlvoResultsGUI::CMD_SHOW));
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
				$this->tabs->addSubTab(self::SUBTAB_SHOW, $this->pl->txt(self::SUBTAB_SHOW), $this->ctrl->getLinkTargetByClass('xlvoplayergui', xlvoPlayerGUI::CMD_STANDARD));
				if ($this->access->hasWriteAccess()) {
					$this->tabs->addSubTab(self::SUBTAB_EDIT, $this->pl->txt(self::SUBTAB_EDIT), $this->ctrl->getLinkTargetByClass('xlvovotinggui', xlvoVotingGUI::CMD_STANDARD));
				}
				break;
		}
		if ($active_subtab) {
			$this->tabs->setSubTabActive($active_subtab);
		}
	}


	public function showContent() {
		$this->ctrl->redirectByClass('xlvoplayergui', xlvoPlayerGUI::CMD_STANDARD);
	}


	public function showContentAfterCreation() {
		$this->ctrl->redirectByClass('xlvovotinggui', xlvoVotingGUI::CMD_STANDARD);
	}


	public function editProperties() {
		if (!$this->access->hasWriteAccess()) {
			\ilUtil::sendFailure($this->pl->txt('obj_permission_denied'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		} else {
			$this->tabs->setTabActive(self::TAB_EDIT);
			$this->initPropertiesForm();
			$this->fillPropertiesForm();
			$this->tpl->setContent($this->form->getHTML());
		}
	}


	/**
	 * @param string $a_new_type
	 * @return array
	 */
	protected function initCreationForms($a_new_type) {
		$forms = array(
			self::CFORM_NEW => $this->initCreateForm($a_new_type),
			self::CFORM_CLONE => $this->fillCloneTemplate(null, $a_new_type)
		);

		return $forms;
	}


	/**
	 * @param string $a_new_type
	 * @return \ilPropertyFormGUI
	 */
	public function initCreateForm($a_new_type) {
		$form = parent::initCreateForm($a_new_type);
		$form->setPreventDoubleSubmission(false);

		return $form;
	}


	protected function initPropertiesForm() {
		if (!$this->access->hasWriteAccess()) {
			\ilUtil::sendFailure(ilLiveVotingPlugin::getInstance()->txt('obj_permission_denied'), true);
		} else {
			$this->form = new \ilPropertyFormGUI();
			$this->form->setTitle($this->pl->txt('obj_edit_properties'));

			$ti = new \ilTextInputGUI($this->pl->txt('obj_title'), self::F_TITLE);
			$ti->setRequired(true);
			$this->form->addItem($ti);
			$ta = new \ilTextAreaInputGUI($this->pl->txt('obj_description'), self::F_DESCRIPTION);
			$this->form->addItem($ta);
			$cb = new \ilCheckboxInputGUI($this->pl->txt('obj_online'), xlvoVotingConfig::F_ONLINE);
			$cb->setInfo($this->pl->txt('obj_info_online'));
			$this->form->addItem($cb);
			$cb = new \ilCheckboxInputGUI($this->pl->txt('obj_anonymous'), xlvoVotingConfig::F_ANONYMOUS);
			$cb->setInfo($this->pl->txt('obj_info_anonymous'));
			$this->form->addItem($cb);

			$cb = new \ilCheckboxInputGUI($this->pl->txt("voting_history"), xlvoVotingConfig::F_VOTING_HISTORY);
			$cb->setInfo($this->pl->txt('voting_history_info'));
			$this->form->addItem($cb);

            $cb = new \ilCheckboxInputGUI($this->pl->txt("show_attendees"), xlvoVotingConfig::F_SHOW_ATTENDEES);
            $cb->setInfo($this->pl->txt('show_attendees_info'));
            $this->form->addItem($cb);

			// Voting Settings
			$h = new \ilFormSectionHeaderGUI();
			$h->setTitle($this->pl->txt('obj_formtitle_change_vote'));
			$this->form->addItem($h);

			$frozen = new \ilRadioGroupInputGUI($this->pl->txt('obj_frozen_behaviour'), xlvoVotingConfig::F_FROZEN_BEHAVIOUR);
			$frozen_always_on = new \ilRadioOption($this->pl->txt('obj_frozen_alway_on'), xlvoVotingConfig::B_FROZEN_ALWAY_ON);
			$frozen_always_on->setInfo($this->pl->txt('obj_frozen_alway_on_info'));
			$frozen->addOption($frozen_always_on);

			$frozen_always_off = new \ilRadioOption($this->pl->txt('obj_frozen_alway_off'), xlvoVotingConfig::B_FROZEN_ALWAY_OFF);
			$frozen_always_off->setInfo($this->pl->txt('obj_frozen_alway_off_info'));
			$frozen->addOption($frozen_always_off);

			$frozen_reuse = new \ilRadioOption($this->pl->txt('obj_frozen_reuse'), xlvoVotingConfig::B_FROZEN_REUSE);
			$frozen_reuse->setInfo($this->pl->txt('obj_frozen_reuse_info'));
			$frozen->addOption($frozen_reuse);

			$this->form->addItem($frozen);

			$results = new \ilRadioGroupInputGUI($this->pl->txt('obj_results_behaviour'), xlvoVotingConfig::F_RESULTS_BEHAVIOUR);
			$results_always_on = new \ilRadioOption($this->pl->txt('obj_results_alway_on'), xlvoVotingConfig::B_RESULTS_ALWAY_ON);
			$results_always_on->setInfo($this->pl->txt('obj_results_alway_on_info'));
			$results->addOption($results_always_on);

			$results_always_off = new \ilRadioOption($this->pl->txt('obj_results_alway_off'), xlvoVotingConfig::B_RESULTS_ALWAY_OFF);
			$results_always_off->setInfo($this->pl->txt('obj_results_alway_off_info'));
			$results->addOption($results_always_off);

			$results_reuse = new \ilRadioOption($this->pl->txt('obj_results_reuse'), xlvoVotingConfig::B_RESULTS_REUSE);
			$results_reuse->setInfo($this->pl->txt('obj_results_reuse_info'));
			$results->addOption($results_reuse);

			$this->form->addItem($results);

			$this->form->addCommandButton('updateProperties', $this->pl->txt('obj_save'));
			$this->form->setFormAction($this->ctrl->getFormAction($this));
		}
	}


	protected function fillPropertiesForm() {

		/**
		 * @var $config xlvoVotingConfig
		 */
		$config = xlvoVotingConfig::find($this->obj_id);

		$values[self::F_TITLE] = $this->object->getTitle();
		$values[self::F_DESCRIPTION] = $this->object->getDescription();
		$values[xlvoVotingConfig::F_ONLINE] = $config->isObjOnline();
		$values[xlvoVotingConfig::F_ANONYMOUS] = $config->isAnonymous();
		$values[xlvoVotingConfig::F_TERMINABLE] = $config->isTerminable();
		$values[xlvoVotingConfig::F_REUSE_STATUS] = $config->isReuseStatus();
		$values[xlvoVotingConfig::F_FROZEN_BEHAVIOUR] = $config->getFrozenBehaviour();
		$values[xlvoVotingConfig::F_RESULTS_BEHAVIOUR] = $config->getResultsBehaviour();
		$values[xlvoVotingConfig::F_VOTING_HISTORY] = $config->getVotingHistory();
		$values[xlvoVotingConfig::F_SHOW_ATTENDEES] = $config->isShowAttendees();

		$this->form->setValuesByArray($values);
	}


	public function updateProperties() {
		if (!$this->access->hasWriteAccess()) {
			\ilUtil::sendFailure(ilLiveVotingPlugin::getInstance()->txt('obj_permission_denied_write'), true);
		} else {
			$this->tabs->setTabActive(self::TAB_EDIT);
			$this->initPropertiesForm();

			if ($this->form->checkInput()) {
				$this->object->setTitle($this->form->getInput(self::F_TITLE));
				$this->object->setDescription($this->form->getInput(self::F_DESCRIPTION));
				$this->object->update();

				/**
				 * @var xlvoVotingConfig $config
				 */
				$config = xlvoVotingConfig::find($this->obj_id);
				$config->setObjOnline($this->form->getInput(xlvoVotingConfig::F_ONLINE));
				$config->setAnonymous($this->form->getInput(xlvoVotingConfig::F_ANONYMOUS));
				$config->setReuseStatus($this->form->getInput(xlvoVotingConfig::F_REUSE_STATUS));
				$terminable = $this->form->getInput(xlvoVotingConfig::F_TERMINABLE);
				$config->setTerminable($terminable);
				$terminable_select = $this->form->getInput(xlvoVotingConfig::F_TERMINABLE_SELECT);
				if ($terminable) {
					$config->setStartDate($this->getDateTimeFromArray($terminable_select['start']));
					$config->setEndDate($this->getDateTimeFromArray($terminable_select['end']));
				} else {
					$config->setStartDate(null);
					$config->setEndDate(null);
				}
				$config->setFrozenBehaviour($this->form->getInput(xlvoVotingConfig::F_FROZEN_BEHAVIOUR));
				$config->setResultsBehaviour($this->form->getInput(xlvoVotingConfig::F_RESULTS_BEHAVIOUR));
				$config->setVotingHistory($this->form->getInput(xlvoVotingConfig::F_VOTING_HISTORY));
                $config->setShowAttendees($this->form->getInput(xlvoVotingConfig::F_SHOW_ATTENDEES));

				$config->update();
				\ilUtil::sendSuccess($this->pl->txt('obj_msg_properties_form_saved'), true);
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
		$date = new \ilDateTime($dt, IL_CAL_FKT_GETDATE, $this->usr->getTimeZone());

		$date->setDate($date, 'yyyy-mm-dd h:m:s');

		return $date->get(IL_CAL_DATETIME, $this->usr->getTimeZone());
	}


	/**
	 * Goto redirection
	 */
	public static function _goto($a_target) {
		if (preg_match("/[\\d]*_pin_([\\w]*)/", $a_target[0], $matches)) {
			xlvoInitialisation::saveContext(xlvoInitialisation::CONTEXT_ILIAS);
			xlvoInitialisation::setCookiePIN($matches[1], true);

			global $ilCtrl;
			/**
			 * @var \ilCtrl $ilCtrl
			 */
			$ilCtrl->initBaseClass('ilUIPluginRouterGUI');
			$ilCtrl->setTargetScript(ltrim(xlvoConf::getFullApiURL(), './'));
			$ilCtrl->redirectByClass(array(
				'ilUIPluginRouterGUI',
				'xlvoVoter2GUI',
			), xlvoVoter2GUI::CMD_START_VOTER_PLAYER);
		}

		parent::_goto($a_target);
	}
}