<?php

require_once __DIR__ . '/../vendor/autoload.php';

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Context\Param\ParamManager;
use LiveVoting\Context\xlvoContext;
use LiveVoting\Context\xlvoInitialisation;
use LiveVoting\Utils\LiveVotingTrait;
use LiveVoting\Voting\xlvoVotingConfig;
use LiveVoting\Voting\xlvoVotingManager2;
use srag\CustomInputGUIs\LiveVoting\TextAreaInputGUI\TextAreaInputGUI;
use srag\CustomInputGUIs\LiveVoting\TextInputGUI\TextInputGUI;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class ilObjLiveVotingGUI
 *
 * @ilCtrl_isCalledBy ilObjLiveVotingGUI: ilRepositoryGUI, ilObjPluginDispatchGUI
 * @ilCtrl_isCalledBy ilObjLiveVotingGUI: ilAdministrationGUI
 * @ilCtrl_Calls      ilObjLiveVotingGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI
 * @ilCtrl_Calls      ilObjLiveVotingGUI: ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilObjLiveVotingGUI: xlvoVoterGUI, xlvoPlayerGUI, xlvoPlayer2GUI
 * @ilCtrl_Calls      ilObjLiveVotingGUI: xlvoVotingGUI, xlvoResultsGUI
 *
 * @author            Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 */
class ilObjLiveVotingGUI extends ilObjectPluginGUI implements ilDesktopItemHandling
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    const CMD_STANDARD = self::CMD_SHOW_CONTENT;
    const CMD_AFTER_CREATION = 'showContentAfterCreation';
    const CMD_SHOW_CONTENT = 'showContent';
    const CMD_EDIT = 'editProperties';
    const CMD_UPDATE = 'updateProperties';
    const TAB_EDIT = 'tab_edit';
    const SUBTAB_SHOW = 'subtab_show';
    const SUBTAB_EDIT = 'subtab_edit';
    const TAB_CONTENT = 'tab_content';
    const TAB_RESULTS = 'tab_results';
    const TAB_PERMISSIONS = 'perm_settings';
    const TAB_LEARNING_PROGRESS = 'learning_progress';
    const F_TITLE = 'title';
    const F_DESCRIPTION = 'description';
    /**
     * @var ilPropertyFormGUI
     */
    protected $form;


    /**
     *
     */
    protected function afterConstructor()
    {

    }


    /**
     * @return string
     */
    public final function getType()
    {
        return ilLiveVotingPlugin::PLUGIN_ID;
    }


    /**
     *
     */
    protected function initHeaderAndLocator()
    {
        // get standard template (includes main menu and general layout)
        if (!self::version()->is6()) {
            self::dic()->ui()->mainTemplate()->getStandardTemplate();
        }
        $this->setTitleAndDescription();
        // set title
        if (!$this->getCreationMode()) {
            self::dic()->ui()->mainTemplate()->setTitle($this->object->getTitle());
            self::dic()->ui()->mainTemplate()->setTitleIcon(ilObject::_getIcon($this->object->getId()));
            self::dic()->ctrl()->saveParameterByClass(xlvoResultsGUI::class, 'round_id');

            // set tabs
            if (strcasecmp($_GET['baseClass'], ilAdministrationGUI::class) != 0) {
                $this->setTabs();
                $this->setLocator();
            } else {
                $this->addAdminLocatorItems();
                self::dic()->ui()->mainTemplate()->setLocator();
                $this->setAdminTabs();
            }

            // add entry to navigation history
            if (self::dic()->access()->checkAccess('read', '', $this->ref_id)) {
                self::dic()->history()->addItem($this->ref_id, self::dic()->ctrl()->getLinkTarget($this, $this->getStandardCmd()), $this->getType());
            }
        } else {
            // show info of parent
            self::dic()->ui()->mainTemplate()->setTitle(ilObject::_lookupTitle(ilObject::_lookupObjId($this->ref_id)));
            self::dic()->ui()->mainTemplate()->setTitleIcon(ilObject::_getIcon(ilObject::_lookupObjId($this->ref_id), 'big'), self::plugin()
                ->translate('obj_' . ilObject::_lookupType($this->ref_id, true)));
            $this->setLocator();
        }
    }


    /**
     * @throws ilCtrlException
     */
    public function executeCommand()
    {
        $this->initHeaderAndLocator();

        self::dic()->ui()->mainTemplate()->setPermanentLink(ilLiveVotingPlugin::PLUGIN_ID, $this->ref_id);

        $next_class = self::dic()->ctrl()->getNextClass($this);
        $cmd = self::dic()->ctrl()->getCmd();

        if (ilObjLiveVotingAccess::hasWriteAccess()
            || ilObjLiveVotingAccess::hasCreateAccess()
            && $_GET["new_type"] == ilLiveVotingPlugin::PLUGIN_ID
        ) {
            $this->triageCmdClass($next_class, $cmd);
        } else {
            $this->redirectToPublicVotingMask();
        }
    }


    /**
     * @param $next_class
     * @param $cmd
     */
    protected function triageCmdClass($next_class, $cmd)
    {
        // TODO: Refactoring
        switch ($next_class) {
            case 'xlvovotinggui':
                $xlvoVotingGUI = new xlvoVotingGUI();
                $this->setSubTabs(self::TAB_CONTENT, self::SUBTAB_EDIT);
                self::dic()->ctrl()->forwardCommand($xlvoVotingGUI);
                break;

            case 'xlvoresultsgui':
                $xlvoResultsGUI = new xlvoResultsGUI($this->obj_id);
                self::dic()->tabs()->activateTab(self::TAB_RESULTS);
                self::dic()->ctrl()->forwardCommand($xlvoResultsGUI);
                break;

            case 'xlvoplayergui':
                $xlvoPlayerGUI = new xlvoPlayerGUI();
                $this->setSubTabs(self::TAB_CONTENT, self::SUBTAB_SHOW);
                self::dic()->ctrl()->forwardCommand($xlvoPlayerGUI);
                break;

            case "ilinfoscreengui":
                $this->checkPermission("visible");
                $this->infoScreen();    // forwards command
                break;

            case 'ilpermissiongui':
                $perm_gui = new ilPermissionGUI($this);
                self::dic()->tabs()->activateTab(self::TAB_PERMISSIONS);
                $ret = self::dic()->ctrl()->forwardCommand($perm_gui);
                break;

            case 'ilobjectcopygui':
                $cp = new ilObjectCopyGUI($this);
                $cp->setType($this->getType());
                self::dic()->ctrl()->forwardCommand($cp);
                break;

            case 'illearningprogressgui':
                self::dic()->tabs()->activateTab(self::TAB_PERMISSIONS);
                $new_gui = new ilLearningProgressGUI(ilLearningProgressGUI::LP_CONTEXT_REPOSITORY, $this->object->getRefId(), $_GET['user_id'] ? $_GET['user_id'] : $GLOBALS['ilUser']->getId());
                self::dic()->ctrl()->forwardCommand($new_gui);
                break;
            case 'ilcommonactiondispatchergui':
                $gui = ilCommonActionDispatcherGUI::getInstanceFromAjaxCall();
                self::dic()->ctrl()->forwardCommand($gui);
                break;

            default:
                if (strcasecmp($_GET['baseClass'], ilAdministrationGUI::class) == 0) {
                    $this->viewObject();

                    return;
                }
                if (!$cmd) {
                    $cmd = $this->getStandardCmd();
                }
                if ($cmd == 'infoScreen') {
                    self::dic()->ctrl()->setCmd('showSummary');
                    self::dic()->ctrl()->setCmdClass(ilInfoScreenGUI::class);
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
            self::output()->output("", true);
        }
    }


    /**
     *
     */
    protected function performCommand()
    {
        self::dic()->help()->setScreenIdComponent(ilLiveVotingPlugin::PLUGIN_ID);

        $cmd = self::dic()->ctrl()->getCmd(self::CMD_STANDARD);
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
    public function getAfterCreationCmd()
    {
        return self::CMD_AFTER_CREATION;
    }


    /**
     * @return string
     */
    public function getStandardCmd()
    {
        return self::CMD_STANDARD;
    }


    /**
     *
     */
    protected function setTabs()
    {
        self::dic()->tabs()->addTab(self::TAB_CONTENT, self::plugin()->translate(self::TAB_CONTENT), self::dic()->ctrl()
            ->getLinkTargetByClass(xlvoPlayerGUI::class, xlvoPlayerGUI::CMD_STANDARD));
        $this->addInfoTab();
        if (ilObjLiveVotingAccess::hasWriteAccess()) {
            self::dic()->tabs()->addTab(self::TAB_EDIT, self::plugin()->translate(self::TAB_EDIT), self::dic()->ctrl()
                ->getLinkTargetByClass(ilObjLiveVotingGUI::class, self::CMD_EDIT));
            self::dic()->tabs()->addTab(self::TAB_RESULTS, self::plugin()->translate(self::TAB_RESULTS), self::dic()->ctrl()
                ->getLinkTargetByClass(xlvoResultsGUI::class, xlvoResultsGUI::CMD_SHOW));
        }
        parent::setTabs();

        return true;
    }


    /**
     * @param $tab
     */
    protected function setSubTabs($tab, $active_subtab = null)
    {
        self::dic()->tabs()->activateTab($tab);
        switch ($tab) {
            case self::TAB_CONTENT:
                self::dic()->tabs()->addSubTab(self::SUBTAB_SHOW, self::plugin()->translate(self::SUBTAB_SHOW), self::dic()->ctrl()
                    ->getLinkTargetByClass(xlvoPlayerGUI::class, xlvoPlayerGUI::CMD_STANDARD));
                if (ilObjLiveVotingAccess::hasWriteAccess()) {
                    self::dic()->tabs()->addSubTab(self::SUBTAB_EDIT, self::plugin()->translate(self::SUBTAB_EDIT), self::dic()->ctrl()
                        ->getLinkTargetByClass(xlvoVotingGUI::class, xlvoVotingGUI::CMD_STANDARD));
                }
                break;
        }
        if ($active_subtab) {
            self::dic()->tabs()->activateSubTab($active_subtab);
        }
    }


    /**
     *
     */
    public function showContent()
    {
        self::dic()->ctrl()->redirectByClass(xlvoPlayerGUI::class, xlvoPlayerGUI::CMD_STANDARD);
    }


    /**
     *
     */
    public function showContentAfterCreation()
    {
        self::dic()->ctrl()->redirectByClass(xlvoVotingGUI::class, xlvoVotingGUI::CMD_STANDARD);
    }


    /**
     *
     */
    public function editProperties()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('obj_permission_denied'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        } else {
            self::dic()->tabs()->activateTab(self::TAB_EDIT);
            $this->initPropertiesForm();
            $this->fillPropertiesForm();
            self::dic()->ui()->mainTemplate()->setContent($this->form->getHTML());
        }
    }


    /**
     * @param string $a_new_type
     *
     * @return array
     */
    protected function initCreationForms($a_new_type)
    {
        $forms = array(
            self::CFORM_NEW   => $this->initCreateForm($a_new_type),
            self::CFORM_CLONE => $this->fillCloneTemplate(null, $a_new_type),
        );

        return $forms;
    }


    /**
     * @param string $a_new_type
     *
     * @return ilPropertyFormGUI
     */
    public function initCreateForm($a_new_type)
    {
        $form = parent::initCreateForm($a_new_type);
        $form->setPreventDoubleSubmission(false);

        return $form;
    }


    /**
     *
     */
    protected function initPropertiesForm()
    {

        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('obj_permission_denied'), true);
        } else {
            $this->form = new ilPropertyFormGUI();
            $this->form->setTitle(self::plugin()->translate('obj_edit_properties'));

            $ti = new TextInputGUI(self::plugin()->translate('obj_title'), self::F_TITLE);
            $ti->setRequired(true);
            $this->form->addItem($ti);
            $ta = new TextAreaInputGUI(self::plugin()->translate('obj_description'), self::F_DESCRIPTION);
            $this->form->addItem($ta);
            $cb = new ilCheckboxInputGUI(self::plugin()->translate('obj_online'), xlvoVotingConfig::F_ONLINE);
            $cb->setInfo(self::plugin()->translate('obj_info_online'));
            $this->form->addItem($cb);
            $cb = new ilCheckboxInputGUI(self::plugin()->translate('obj_anonymous'), xlvoVotingConfig::F_ANONYMOUS);
            $cb->setInfo(self::plugin()->translate('obj_info_anonymous'));
            $this->form->addItem($cb);

            $cb = new ilCheckboxInputGUI(self::plugin()->translate("voting_history"), xlvoVotingConfig::F_VOTING_HISTORY);
            $cb->setInfo(self::plugin()->translate('voting_history_info'));
            $this->form->addItem($cb);

            $cb = new ilCheckboxInputGUI(self::plugin()->translate("show_attendees"), xlvoVotingConfig::F_SHOW_ATTENDEES);
            $cb->setInfo(self::plugin()->translate('show_attendees_info'));
            $this->form->addItem($cb);

            // Voting Settings
            $h = new ilFormSectionHeaderGUI();
            $h->setTitle(self::plugin()->translate('obj_formtitle_change_vote'));
            $this->form->addItem($h);

            $frozen = new ilRadioGroupInputGUI(self::plugin()->translate('obj_frozen_behaviour'), xlvoVotingConfig::F_FROZEN_BEHAVIOUR);
            $frozen_always_on = new ilRadioOption(self::plugin()->translate('obj_frozen_alway_on'), xlvoVotingConfig::B_FROZEN_ALWAY_ON);
            $frozen_always_on->setInfo(self::plugin()->translate('obj_frozen_alway_on_info'));
            $frozen->addOption($frozen_always_on);

            $frozen_always_off = new ilRadioOption(self::plugin()->translate('obj_frozen_alway_off'), xlvoVotingConfig::B_FROZEN_ALWAY_OFF);
            $frozen_always_off->setInfo(self::plugin()->translate('obj_frozen_alway_off_info'));
            $frozen->addOption($frozen_always_off);

            $frozen_reuse = new ilRadioOption(self::plugin()->translate('obj_frozen_reuse'), xlvoVotingConfig::B_FROZEN_REUSE);
            $frozen_reuse->setInfo(self::plugin()->translate('obj_frozen_reuse_info'));
            $frozen->addOption($frozen_reuse);

            $this->form->addItem($frozen);

            $results = new ilRadioGroupInputGUI(self::plugin()->translate('obj_results_behaviour'), xlvoVotingConfig::F_RESULTS_BEHAVIOUR);
            $results_always_on = new ilRadioOption(self::plugin()->translate('obj_results_alway_on'), xlvoVotingConfig::B_RESULTS_ALWAY_ON);
            $results_always_on->setInfo(self::plugin()->translate('obj_results_alway_on_info'));
            $results->addOption($results_always_on);

            $results_always_off = new ilRadioOption(self::plugin()->translate('obj_results_alway_off'), xlvoVotingConfig::B_RESULTS_ALWAY_OFF);
            $results_always_off->setInfo(self::plugin()->translate('obj_results_alway_off_info'));
            $results->addOption($results_always_off);

            $results_reuse = new ilRadioOption(self::plugin()->translate('obj_results_reuse'), xlvoVotingConfig::B_RESULTS_REUSE);
            $results_reuse->setInfo(self::plugin()->translate('obj_results_reuse_info'));
            $results->addOption($results_reuse);

            $this->form->addItem($results);

            $this->form->addCommandButton('updateProperties', self::plugin()->translate('obj_save'));
            $this->form->setFormAction(self::dic()->ctrl()->getFormAction($this));
        }
    }


    /**
     *
     */
    protected function fillPropertiesForm()
    {
        /**
         * @var xlvoVotingConfig $config
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


    /**
     *
     */
    public function updateProperties()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('obj_permission_denied_write'), true);
        } else {
            self::dic()->tabs()->activateTab(self::TAB_EDIT);
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

                $config->store();
                ilUtil::sendSuccess(self::plugin()->translate('obj_msg_properties_form_saved'), true);
                self::dic()->ctrl()->redirect($this, self::CMD_EDIT);
            }

            $this->form->setValuesByPost();
            self::dic()->ui()->mainTemplate()->setContent($this->form->getHtml());
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
    protected function getDateTimeFromArray($a_field)
    {
        $dt['year'] = (int) $a_field['date']['y'];
        $dt['mon'] = (int) $a_field['date']['m'];
        $dt['mday'] = (int) $a_field['date']['d'];
        $dt['hours'] = (int) $a_field['time']['h'];
        $dt['minutes'] = (int) $a_field['time']['m'];
        $dt['seconds'] = (int) $a_field['time']['s'];
        $date = new ilDateTime($dt, IL_CAL_FKT_GETDATE, self::dic()->user()->getTimeZone());

        $date->setDate($date, 'yyyy-mm-dd h:m:s');

        return $date->get(IL_CAL_DATETIME, self::dic()->user()->getTimeZone());
    }


    /**
     * Goto redirection
     */
    public static function _goto($a_target)
    {
        if (preg_match("/[\\d]*_pin_([\\w]*)/", $a_target[0], $matches)) {
            xlvoInitialisation::saveContext(xlvoInitialisation::CONTEXT_ILIAS);

            $param_manager = ParamManager::getInstance();
            $param_manager->setPin($matches[1]);

            //self::dic()->ctrl()->initBaseClass(ilUIPluginRouterGUI::class);
            self::dic()->ctrl()->setTargetScript(ltrim(xlvoConf::getFullApiURL(), './'));
            self::dic()->ctrl()->redirectByClass(array(
                ilUIPluginRouterGUI::class,
                xlvoVoter2GUI::class,
            ), xlvoVoter2GUI::CMD_START_VOTER_PLAYER);
        }

        parent::_goto($a_target);
    }


    /**
     *
     */
    protected function redirectToPublicVotingMask()
    {
        $xlvoVotingManager2 = xlvoVotingManager2::getInstanceFromObjId($this->obj_id);

        $param_manager = ParamManager::getInstance();
        $param_manager->setPin($xlvoVotingManager2->getVotingConfig()->getPin());
        xlvoContext::setContext(xlvoContext::CONTEXT_ILIAS);

        self::dic()->ctrl()->setTargetScript(xlvoConf::getFullApiURL());
        self::dic()->ctrl()->redirectByClass(array(
            ilUIPluginRouterGUI::class,
            xlvoVoter2GUI::class,
        ));
    }


    /**
     *
     */
    public function addToDeskObject()
    {
        ilDesktopItemGUI::addToDesktop();
        ilUtil::sendSuccess(self::dic()->language()->txt("added_to_desktop"));
    }


    /**
     *
     */
    public function removeFromDeskObject()
    {
        ilDesktopItemGUI::removeFromDesktop();
        ilUtil::sendSuccess(self::dic()->language()->txt("removed_from_desktop"));
    }
}
