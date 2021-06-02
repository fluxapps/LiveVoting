<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Option\xlvoOption;
use LiveVoting\PowerPointExport\PowerPointExport;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use LiveVoting\Round\xlvoRound;
use LiveVoting\Utils\LiveVotingTrait;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\DuplicateToAnotherObjectSelectFormGUI;
use LiveVoting\Voting\xlvoVoting;
use LiveVoting\Voting\xlvoVotingConfig;
use LiveVoting\Voting\xlvoVotingFormGUI;
use LiveVoting\Voting\xlvoVotingTableGUI;
use srag\CustomInputGUIs\LiveVoting\GlyphGUI\GlyphGUI;
use srag\DIC\LiveVoting\DICTrait;

/**
 *
 * Class xlvoVotingGUI
 *
 * @author            Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_Calls      xlvoVotingGUI: xlvoSingleVoteVotingGUI, xlvoFreeInputVotingGUI
 * @ilCtrl_Calls      xlvoVotingGUI: ilPropertyFormGUI
 *
 */
class xlvoVotingGUI
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
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
    const CMD_DUPLICATE_TO_ANOTHER_OBJECT = 'duplicateToAnotherObject';
    const CMD_DUPLICATE_TO_ANOTHER_OBJECT_SELECT = 'duplicateToAnotherObjectSelect';
    const CMD_RESET = 'reset';
    const CMD_CONFIRM_RESET_ALL = 'confirmResetAll';
    const CMD_RESET_ALL = 'resetAll';
    const CMD_CANCEL = 'cancel';
    const CMD_BACK = 'back';
    const CMD_EXPORT = 'export';
    const CMD_IMPORT = 'import';
    const CMD_POWERPOINT_EXPORT = 'powerPointExport';
    const F_TYPE = 'type';
    /**
     * @var ilObjLiveVotingAccess
     */
    protected $access;
    /**
     * @var int
     */
    protected $obj_id;
    /**
     * @var xlvoRound
     */
    protected $round;
    /**
     * @var ilObjLiveVoting
     */
    protected $obj;


    /**
     *
     */
    public function __construct()
    {
        $this->access = new ilObjLiveVotingAccess();
        $ref_id = filter_input(INPUT_GET, 'ref_id');
        $this->obj_id = ilObject2::_lookupObjId($ref_id);
        $this->round = xlvoRound::getLatestRound($this->obj_id);
        $this->obj = new ilObjLiveVoting($ref_id);
    }


    /**
     *
     */
    public function executeCommand()
    {
        $nextClass = self::dic()->ctrl()->getNextClass();
        switch ($nextClass) {
            default:
                $cmd = self::dic()->ctrl()->getCmd(self::CMD_STANDARD);
                $this->{$cmd}();
                break;
        }
    }


    /**
     *
     */
    protected function content()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
        } elseif (ilObjLiveVotingAccess::hasWriteAccess()) {
            $b = ilLinkButton::getInstance();
            $b->setPrimary(true);
            $b->setCaption($this->txt('add'), false);
            $b->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_SELECT_TYPE));
            self::dic()->toolbar()->addButtonInstance($b);

            $collection = xlvoVoting::where(array('obj_id' => $this->getObjId()))
                ->where(array('voting_type' => xlvoQuestionTypes::getActiveTypes()))->orderBy('position', 'ASC');
            if (xlvoConf::getConfig(xlvoConf::F_ACTIVATE_POWERPOINT_EXPORT) && $collection->count() > 0) {
                $powerpoint_export = ilLinkButton::getInstance();
                $powerpoint_export->setCaption($this->txt('powerpoint_export'), false);
                $powerpoint_export->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_POWERPOINT_EXPORT));
                self::dic()->toolbar()->addButtonInstance($powerpoint_export);
            }

            $voting_ids = xlvoVoting::where(array('obj_id' => $this->obj_id))->getArray(null, 'id');
            $has_votes = false;
            if (count($voting_ids) > 0) {
                $has_votes = xlvoVote::where(array(
                    'voting_id' => $voting_ids,
                    'round_id'  => $this->round->getId(),
                ))->hasSets();
            }

            $b = ilLinkButton::getInstance();
            $b->setDisabled(!$has_votes);
            $b->setCaption($this->txt('reset_all'), false);
            $b->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_CONFIRM_RESET_ALL));
            self::dic()->toolbar()->addButtonInstance($b);

            if ($_GET['import']) {
                $b = ilLinkButton::getInstance();
                $b->setCaption($this->txt('export'), false);
                $b->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_EXPORT));
                self::dic()->toolbar()->addButtonInstance($b);

                self::dic()->toolbar()->setFormAction(self::dic()->ctrl()->getLinkTarget($this, self::CMD_IMPORT), true);
                $import = new ilFileInputGUI('xlvo_import', 'xlvo_import');
                self::dic()->toolbar()->addInputItem($import);
                $button = ilSubmitButton::getInstance();
                $button->setCaption($this->txt('import'), false);
                $button->setCommand(self::CMD_IMPORT);
                self::dic()->toolbar()->addButtonInstance($button);
            }

            $xlvoVotingTableGUI = new xlvoVotingTableGUI($this, self::CMD_STANDARD);

            /**
             * @var xlvoVotingConfig $config
             */
            $config = xlvoVotingConfig::find($this->obj_id);

            $power_point_enabled = xlvoConf::getConfig(xlvoConf::F_ACTIVATE_POWERPOINT_EXPORT);

            $powerpoint_export = ($power_point_enabled ? '<br><br><i>'
                . htmlspecialchars(self::plugin()->translate("config_" . xlvoConf::F_ACTIVATE_POWERPOINT_EXPORT . "_info_manual")) . '</i><ol>'
                . implode("", array_map(function ($step) {
                    return '<li>' . htmlspecialchars(self::plugin()->translate("config_" . xlvoConf::F_ACTIVATE_POWERPOINT_EXPORT
                            . "_info_manual_" . $step)) . '</li>';
                }, range(1, 4))) . '</ol>' : ''); // TODO: default.css not loaded

            self::dic()->mainTemplate()->setContent($xlvoVotingTableGUI->getHTML() . $powerpoint_export);
        }
    }


    /**
     *
     */
    protected function selectType()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        } else {
            $form = new ilPropertyFormGUI();
            $form->setFormAction(self::dic()->ctrl()->getFormAction($this, self::CMD_ADD));
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

            self::dic()->mainTemplate()->setContent($form->getHTML());
        }
    }


    /**
     *
     */
    protected function add()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        } else {
            $xlvoVoting = new xlvoVoting();
            $xlvoVoting->setVotingType($_POST[self::F_TYPE]);
            $xlvoVotingFormGUI = xlvoVotingFormGUI::get($this, $xlvoVoting);
            $xlvoVotingFormGUI->fillForm();
            self::dic()->mainTemplate()->setContent($xlvoVotingFormGUI->getHTML());
        }
    }


    /**
     *
     */
    protected function create()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        } else {
            $xlvoVoting = new xlvoVoting();
            $xlvoVoting->setVotingType($_POST[self::F_TYPE]);
            $xlvoVotingFormGUI = xlvoVotingFormGUI::get($this, $xlvoVoting);
            $xlvoVotingFormGUI->setValuesByPost();
            if ($xlvoVotingFormGUI->saveObject()) {
                ilUtil::sendSuccess(self::plugin()->translate('msg_success_voting_created'), true);
                self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
            }
            self::dic()->mainTemplate()->setContent($xlvoVotingFormGUI->getHTML());
        }
    }


    /**
     *
     */
    protected function edit()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        } else {
            /**
             * @var xlvoVoting $xlvoVoting
             */
            $xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);
            // PREV
            $prev_id = xlvoVoting::where(array(
                'obj_id'        => $xlvoVoting->getObjId(),
                'voting_status' => xlvoVoting::STAT_ACTIVE,
            ))->orderBy('position', 'DESC')->where(array('position' => $xlvoVoting->getPosition()), '<')->limit(0, 1)->getArray('id', 'id');
            $array1 = array_values($prev_id);
            $prev_id = array_shift($array1);

            if ($prev_id) {
                self::dic()->ctrl()->setParameter($this, self::IDENTIFIER, $prev_id);
                $prev = ilLinkButton::getInstance();
                $prev->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT));
                $prev->setCaption(GlyphGUI::get(GlyphGUI::PREVIOUS), false);
                self::dic()->toolbar()->addButtonInstance($prev);
            }

            // NEXT
            $next_id = xlvoVoting::where(array(
                'obj_id'        => $xlvoVoting->getObjId(),
                'voting_status' => xlvoVoting::STAT_ACTIVE,
            ))->orderBy('position', 'ASC')->where(array('position' => $xlvoVoting->getPosition()), '>')->limit(0, 1)->getArray('id', 'id');
            $array = array_values($next_id);
            $next_id = array_shift($array);

            if ($next_id) {
                self::dic()->ctrl()->setParameter($this, self::IDENTIFIER, $next_id);
                $next = ilLinkButton::getInstance();
                $next->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT));
                $next->setCaption(GlyphGUI::get(GlyphGUI::NEXT), false);
                self::dic()->toolbar()->addButtonInstance($next);
            }
            self::dic()->ctrl()->setParameter($this, self::IDENTIFIER, $xlvoVoting->getId());
            $xlvoVotingFormGUI = xlvoVotingFormGUI::get($this, $xlvoVoting);
            $xlvoVotingFormGUI->fillForm();

            $h = new ilFormSectionHeaderGUI();
            $h->setTitle("");
            $xlvoVotingFormGUI->addItem($h);

            /**
             * @var xlvoVotingConfig $config
             */
            $config = xlvoVotingConfig::find($this->obj_id);

            $power_point_enabled = xlvoConf::getConfig(xlvoConf::F_ACTIVATE_POWERPOINT_EXPORT);

            $presenter_link = new ilCustomInputGUI(self::plugin()->translate('config_presenter_link'));

            if ($config->isAnonymous()) {
                $presenter_link->setHtml($config->getPresenterLink($xlvoVoting->getId(), $power_point_enabled, false, !$power_point_enabled)
                    . ($power_point_enabled ? '<br><br><i>' . htmlspecialchars(self::plugin()->translate("config_ppt_link_info_manual")) . '</i><ol>' . implode("", array_map(function ($step) {
                            return '<li>' . htmlspecialchars(self::plugin()->translate("config_ppt_link_info_manual_" . $step)) . '</li>';
                        }, range(1, 6))) . '</ol>' : ''));
            } else {
                $presenter_link->setHtml(self::plugin()->translate("config_presenter_link_non_anonym"));
            }

            $xlvoVotingFormGUI->addItem($presenter_link);

            self::dic()->mainTemplate()->setContent($xlvoVotingFormGUI->getHTML());
        }
    }


    /**
     *
     */
    public function updateAndStay()
    {
        self::dic()->ctrl()->setParameter($this, self::IDENTIFIER, filter_input(INPUT_GET, self::IDENTIFIER));
        $this->update(self::CMD_EDIT);
    }


    /**
     * @param string $cmd
     */
    protected function update($cmd = self::CMD_STANDARD)
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        } else {
            $xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);
            self::dic()->ctrl()->setParameter($this, self::IDENTIFIER, $xlvoVoting->getId());
            $xlvoVotingFormGUI = xlvoVotingFormGUI::get($this, $xlvoVoting);
            $xlvoVotingFormGUI->setValuesByPost();
            if ($xlvoVotingFormGUI->saveObject()) {
                ilUtil::sendSuccess(self::plugin()->translate('msg_success_voting_updated'), true);
                self::dic()->ctrl()->redirect($this, $cmd);
            }
            self::dic()->mainTemplate()->setContent($xlvoVotingFormGUI->getHTML());
        }
    }


    /**
     *
     */
    protected function confirmDelete()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        } else {

            /**
             * @var xlvoVoting $xlvoVoting
             */
            $xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);

            if ($xlvoVoting->getObjId() == $this->getObjId()) {
                ilUtil::sendQuestion($this->txt('delete_confirm'), true);
                $confirm = new ilConfirmationGUI();
                $confirm->addItem(self::IDENTIFIER, $xlvoVoting->getId(), $xlvoVoting->getTitle());
                $confirm->setFormAction(self::dic()->ctrl()->getFormAction($this));
                $confirm->setCancel($this->txt('cancel'), self::CMD_CANCEL);
                $confirm->setConfirm($this->txt('delete'), self::CMD_DELETE);

                self::dic()->mainTemplate()->setContent($confirm->getHTML());
            } else {
                ilUtil::sendFailure(self::plugin()->translate('permission_denied_object'), true);
                self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
            }
        }
    }


    /**
     *
     */
    protected function delete()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        } else {

            /**
             * @var xlvoVoting $xlvoVoting
             */
            $xlvoVoting = xlvoVoting::find($_POST[self::IDENTIFIER]);

            if ($xlvoVoting->getObjId() == $this->getObjId()) {
                /**
                 * @var xlvoOption[] $options
                 */
                $options = xlvoOption::where(array('voting_id' => $xlvoVoting->getId()))->get();
                foreach ($options as $option) {
                    $option->delete();
                }
                /**
                 * @var xlvoVote[] $votes
                 */
                $votes = xlvoVote::where(array('voting_id' => $xlvoVoting->getId()))->get();
                foreach ($votes as $vote) {
                    $vote->delete();
                }
                $xlvoVoting->delete();
                $this->cancel();
            } else {
                ilUtil::sendFailure(self::plugin()->translate('delete_failed'), true);
                self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
            }
        }
    }


    /**
     *
     */
    protected function confirmReset()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        } else {

            /**
             * @var xlvoVoting $xlvoVoting
             */
            $xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);

            if ($xlvoVoting->getObjId() == $this->getObjId()) {
                ilUtil::sendQuestion($this->txt('confirm_reset'), true);
                $confirm = new ilConfirmationGUI();
                $confirm->addItem(self::IDENTIFIER, $xlvoVoting->getId(), $xlvoVoting->getTitle());
                $confirm->setFormAction(self::dic()->ctrl()->getFormAction($this));
                $confirm->setCancel($this->txt('cancel'), self::CMD_CANCEL);
                $confirm->setConfirm($this->txt('reset'), self::CMD_RESET);

                self::dic()->mainTemplate()->setContent($confirm->getHTML());
            } else {
                ilUtil::sendFailure($this->txt('permission_denied_object'), true);
                self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
            }
        }
    }


    /**
     *
     */
    protected function reset()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        } else {
            /**
             * @var xlvoVoting $xlvoVoting
             */
            $xlvoVoting = xlvoVoting::find($_POST[self::IDENTIFIER]);

            if ($xlvoVoting->getObjId() == $this->getObjId()) {

                /**
                 * @var xlvoVote[] $votes
                 */
                $votes = xlvoVote::where(array('voting_id' => $xlvoVoting->getId()))->get();
                foreach ($votes as $vote) {
                    $vote->delete();
                }
                $this->cancel();
            } else {
                ilUtil::sendFailure(self::plugin()->translate('reset_failed'), true);
                self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
            }
        }
    }


    /**
     *
     */
    protected function confirmResetAll()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        } else {
            ilUtil::sendQuestion($this->txt('confirm_reset_all'), true);
            $confirm = new ilConfirmationGUI();
            /**
             * @var xlvoVoting[] $votings
             */
            $votings = xlvoVoting::where(array('obj_id' => $this->getObjId()))->get();
            $num_votes = 0;
            foreach ($votings as $voting) {
                $num_votes += xlvoVote::where(array('voting_id' => $voting->getId()))->count();
            }
            $confirm->addItem(self::IDENTIFIER, 0, $this->txt('confirm_number_of_votes') . " " . $num_votes);
            $confirm->setFormAction(self::dic()->ctrl()->getFormAction($this));
            $confirm->setCancel($this->txt('cancel'), self::CMD_CANCEL);
            $confirm->setConfirm($this->txt('reset_all'), self::CMD_RESET_ALL);

            self::dic()->mainTemplate()->setContent($confirm->getHTML());
        }
    }


    /**
     *
     */
    protected function resetAll()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        } else {
            /**
             * @var xlvoVoting[] $votings
             */
            $votings = xlvoVoting::where(array('obj_id' => $this->getObjId()))->get();
            foreach ($votings as $voting) {
                /**
                 * @var xlvoVote[] $votes
                 */
                $votes = xlvoVote::where(array('voting_id' => $voting->getId()))->get();
                foreach ($votes as $vote) {
                    $vote->delete();
                }
            }

            $this->cancel();
        }
    }


    /**
     *
     */
    protected function duplicate()
    {
        /**
         * @var xlvoVoting $xlvoVoting
         */
        $xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);
        $xlvoVoting->fullClone(true, true);
        ilUtil::sendSuccess(self::plugin()->translate('voting_msg_duplicated'), true);
        $this->cancel();
    }


    /**
     * @return DuplicateToAnotherObjectSelectFormGUI
     */
    public function getDuplicateToAnotherObjectSelectForm()
    {
        self::dic()->ctrl()->setParameter($this, self::IDENTIFIER, $_GET[self::IDENTIFIER]);

        $form = new DuplicateToAnotherObjectSelectFormGUI($this);

        return $form;
    }


    /**
     *
     */
    protected function duplicateToAnotherObjectSelect()
    {
        $form = $this->getDuplicateToAnotherObjectSelectForm();

        self::output()->output($form);
    }


    /**
     *
     */
    protected function handleExplorerCommand()
    {
        $form = $this->getDuplicateToAnotherObjectSelectForm();

        $form->getItemByPostVar("ref_id")->handleExplorerCommand();
    }


    /**
     *
     */
    protected function duplicateToAnotherObject()
    {
        $form = $this->getDuplicateToAnotherObjectSelectForm();

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        $obj_id = self::dic()->objDataCache()->lookupObjId($form->getInput("ref_id"));

        /**
         * @var xlvoVoting $xlvoVoting
         */
        $xlvoVoting = xlvoVoting::find($_GET[self::IDENTIFIER]);
        $xlvoVoting->fullClone(true, true, $obj_id);
        ilUtil::sendSuccess(self::plugin()->translate('voting_msg_duplicated'), true);
        $this->cancel();
    }


    /**
     *
     */
    protected function cancel()
    {
        self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
    }


    /**
     *
     */
    protected function saveSorting()
    {
        if (!ilObjLiveVotingAccess::hasWriteAccess()) {
            ilUtil::sendFailure(self::plugin()->translate('permission_denied_write'), true);
        } else {
            if (is_array($_POST['position'])) {
                foreach ($_POST['position'] as $k => $v) {
                    /**
                     * @var xlvoVoting $xlvoVoting
                     */
                    $xlvoVoting = xlvoVoting::find($v);
                    $xlvoVoting->setPosition($k + 1);
                    $xlvoVoting->store();
                }
            }
            ilUtil::sendSuccess(self::plugin()->translate('voting_msg_sorting_saved'), true);
            self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
        }
    }


    /**
     *
     */
    protected function applyFilter()
    {
        $xlvoVotingGUI = new xlvoVotingTableGUI($this, self::CMD_STANDARD);
        $xlvoVotingGUI->writeFilterToSession();
        self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
    }


    /**
     *
     */
    protected function resetFilter()
    {
        $xlvoVotingTableGUI = new xlvoVotingTableGUI($this, self::CMD_STANDARD);
        $xlvoVotingTableGUI->resetFilter();
        $xlvoVotingTableGUI->resetOffset();
        self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
    }


    /**
     * @return int
     */
    public function getObjId()
    {
        return $this->obj_id;
    }


    /**
     * @param int $obj_id
     */
    public function setObjId($obj_id)
    {
        $this->obj_id = $obj_id;
    }


    /**
     * @param string $key
     *
     * @return string
     */
    public function txt($key)
    {
        return self::plugin()->translate($key, 'voting');
    }


    /**
     *
     */
    protected function export()
    {
        $domxml = new DOMDocument('1.0', 'UTF-8');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $config = $domxml->appendChild(new DOMElement(ilLiveVotingPlugin::PLUGIN_NAME));

        $xml_info = $config->appendChild(new DOMElement('info'));
        $xml_info->appendChild(new DOMElement('plugin_version', self::plugin()->getPluginObject()->getVersion()));
        $xml_info->appendChild(new DOMElement('plugin_db_version', self::plugin()->getPluginObject()->getDBVersion()));

        // xoctConf
        $xml_votings = $config->appendChild(new DOMElement('votings'));

        /**
         * @var xlvoVoting $xlvoVoting
         * @var xlvoOption $xlvoOption
         */
        foreach (xlvoVoting::where(array('obj_id' => $this->getObjId()))->get() as $xlvoVoting) {
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
            $xml_voting->appendChild(new DOMElement('percentage'))->appendChild(new DOMCdataSection($xlvoVoting->getPercentage()));
            $xml_voting->appendChild(new DOMElement('start_range'))->appendChild(new DOMCdataSection($xlvoVoting->getStartRange()));
            $xml_voting->appendChild(new DOMElement('end_range'))->appendChild(new DOMCdataSection($xlvoVoting->getEndRange()));
            $xml_voting->appendChild(new DOMElement('alt_result_display_mode'))
                ->appendChild(new DOMCdataSection($xlvoVoting->getAltResultDisplayMode()));
            $xml_voting->appendChild(new DOMElement('randomise_option_sequence'))
                ->appendChild(new DOMCdataSection($xlvoVoting->getRandomiseOptionSequence()));

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


    /**
     *
     */
    protected function import()
    {
        $domxml = new DOMDocument('1.0', 'UTF-8');
        $domxml->loadXML(file_get_contents($_FILES['xlvo_import']['tmp_name']));

        /**
         * @var DOMElement $node
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
            $percentage = $node->getElementsByTagName('percentage')->item(0)->nodeValue;
            $start_range = $node->getElementsByTagName('start_range')->item(0)->nodeValue;
            $end_range = $node->getElementsByTagName('end_range')->item(0)->nodeValue;
            $alt_result_display_mode = $node->getElementsByTagName('alt_result_display_mode')->item(0)->nodeValue;
            $randomise_option_sequence = $node->getElementsByTagName('randomise_option_sequence')->item(0)->nodeValue;

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
            $xlvoVoting->setPercentage($percentage);
            $xlvoVoting->setStartRange($start_range);
            $xlvoVoting->setEndRange($end_range);
            $xlvoVoting->setAltResultDisplayMode($alt_result_display_mode);
            $xlvoVoting->setRandomiseOptionSequence($randomise_option_sequence);

            $xlvoVoting->store();

            $options = $node->getElementsByTagName('option');
            $xlvoOptions = array();
            /**
             * @var DOMElement $option
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
                $xlvoOption->store();

                $xlvoOptions[] = $xlvoOption;
            }
            $xlvoVoting->setVotingOptions($xlvoOptions);
            $xlvoVoting->regenerateOptionSorting();
        }
        $this->cancel();
    }


    /**
     *
     */
    protected function powerPointExport()
    {
        $powerPointExport = new PowerPointExport($this->obj);
        $powerPointExport->run();
    }
}
