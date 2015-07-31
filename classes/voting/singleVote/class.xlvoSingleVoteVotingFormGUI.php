<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoMultiLineInputGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoOption.php');

class xlvoSingleVoteVotingFormGUI extends xlvoVotingFormGUI
{

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
     * @var xlvoOption[]
     */
    protected $options_map;


    /**
     * @param              $parent_gui
     * @param xlvoVoting $xlvoVoting
     * @param xlvoOption $xlvoOption
     */
    public function __construct($parent_gui, xlvoVoting $xlvoVoting)
    {
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
        $this->options_map = array();

        $this->initForm();
    }


    protected function initForm()
    {
        $this->setTarget('_top');
        $this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
        $this->initButtons();

        $cb = new ilCheckboxInputGUI($this->pl->txt('multi_selection'), 'multi_selection');
        $this->addItem($cb);
        $cb = new ilCheckboxInputGUI($this->pl->txt('colors'), 'colors');
        $this->addItem($cb);
        $mli = new xlvoMultiLineInputGUI($this->pl->txt('options'), 'options');
        $te = new ilTextInputGUI($this->pl->txt('text'), 'text');
        $mli->addInput($te);
        $this->addItem($mli);
    }


    public function fillForm()
    {
        if ($this->voting->getObjId() == $this->parent_gui->getObjId()) {

            $this->options = xlvoOption::where(array('voting_id' => $this->voting->getId(),
                'status' => xlvoOption::STAT_ACTIVE))->getArray();

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
    public function fillObject()
    {
        if (!$this->checkInput()) {
            return false;
        }

        $this->voting->setMultiSelection($this->getInput('multi_selection'));
        $this->voting->setColors($this->getInput('colors'));
        $opts = $this->getInput('options');

        $this->options = xlvoOption::where(array('voting_id' => $this->voting->getId(),
            'status' => xlvoOption::STAT_ACTIVE))->getArray();

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
                $option = new xlvoOption();
                $option->setVotingId($this->voting->getId());
                $option->setType($this->voting->getVotingType());
                $option->setId($id);
                $option->setText($arr_existing_texts[$id]);
                $option->setStatus(xlvoOption::STAT_INACTIVE);
                array_push($this->options, $option);
            }
        }

        return true;
    }


    /**
     * @return bool|string
     */
    public function saveObject()
    {
        if (!$this->fillObject()) {
            return false;
        }
        if ($this->voting->getObjId() == $this->parent_gui->getObjId()) {

            foreach ($this->options as $option) {
                if ($this->voting->getId() == $option->getVotingId()) {
                    if (!xlvoOption::where(array('id' => $option->getId()))->hasSets()) {
                        $option->create();
                    } else {
                        $option->update();
                    }
                } else {
                    ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
                    $this->ctrl->redirect($this->parent_gui, xlvoVoting::CMD_STANDARD);
                }
            }

            if (xlvoVoting::where(array('id' => $this->voting->getId()))->hasSets()) {
                $this->voting->setVotingStatus(xlvoVoting::STAT_INACTIVE);
                $this->voting->update();
            }
        } else {
            ilUtil::sendFailure($this->pl->txt('permission_denied'), true);
            $this->ctrl->redirect($this->parent_gui, xlvoVoting::CMD_STANDARD);
        }

        return true;
    }


    protected function initButtons()
    {
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