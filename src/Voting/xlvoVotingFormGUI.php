<?php

namespace LiveVoting\Voting;

use ilException;
use ilHiddenInputGUI;
use ilLiveVotingPlugin;
use ilNonEditableValueGUI;
use ilObject;
use ilPropertyFormGUI;
use ilRTE;
use ilSelectInputGUI;
use ilUtil;
use LiveVoting\Exceptions\xlvoSubFormGUIHandleFieldException;
use LiveVoting\QuestionTypes\FreeInput\xlvoFreeInputVotingFormGUI;
use LiveVoting\QuestionTypes\NumberRange\xlvoNumberRangeVotingFormGUI;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use LiveVoting\QuestionTypes\xlvoSubFormGUI;
use LiveVoting\Utils\LiveVotingTrait;
use srag\CustomInputGUIs\LiveVoting\TextAreaInputGUI\TextAreaInputGUI;
use srag\CustomInputGUIs\LiveVoting\TextInputGUI\TextInputGUI;
use srag\DIC\LiveVoting\DICTrait;
use xlvoVotingGUI;
use ILIAS\DI\Container;

/**
 * Class xlvoVotingFormGUI
 *
 * @package LiveVoting\Voting
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoVotingFormGUI extends ilPropertyFormGUI
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    const F_COLUMNS = 'columns';
    const USE_F_COLUMNS = true;
    /**
     * @var xlvoVoting
     */
    protected $voting;
    /**
     * @var xlvoVotingGUI
     */
    protected $parent_gui;
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
     * @param xlvoVoting    $xlvoVoting
     *
     * @return xlvoVotingFormGUI
     */
    public static function get(xlvoVotingGUI $parent_gui, xlvoVoting $xlvoVoting)
    {
        switch ($xlvoVoting->getVotingType()) {
            case xlvoQuestionTypes::TYPE_FREE_INPUT:
                return new xlvoFreeInputVotingFormGUI($parent_gui, $xlvoVoting);

            case xlvoQuestionTypes::TYPE_NUMBER_RANGE:
                return new xlvoNumberRangeVotingFormGUI($parent_gui, $xlvoVoting);

            default:
                return new self($parent_gui, $xlvoVoting);
        }
    }


    /**
     * @param xlvoVotingGUI $parent_gui
     * @param xlvoVoting    $xlvoVoting
     */
    public function __construct(xlvoVotingGUI $parent_gui, xlvoVoting $xlvoVoting)
    {
        parent::__construct();

        $this->voting = $xlvoVoting;
        $this->parent_gui = $parent_gui;
        $this->is_new = ($this->voting->getId() == '');

        $this->initForm();
    }


    /**
     *
     */
    protected function initForm()
    {
        if ($this->is_new) {
            $h = new ilHiddenInputGUI('type');
            $this->addItem($h);
        }

        $this->setTarget('_top');
        $this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent_gui));
        $this->initButtons();

        $te = new ilNonEditableValueGUI($this->parent_gui->txt('type'));
        $te->setValue($this->txt('type_' . $this->voting->getVotingType()));
        $te->setInfo($this->txt('type_' . $this->voting->getVotingType() . "_info"));
        $this->addItem($te);

        $te = new TextInputGUI($this->parent_gui->txt('title'), 'title');
        //		$te->setInfo($this->parent_gui->txt('info_voting_title'));
        $te->setRequired(true);
        $this->addItem($te);

        $ta = new TextAreaInputGUI($this->parent_gui->txt('description'), 'description');
        //		$ta->setInfo($this->parent_gui->txt('info_voting_description'));
        //		$this->addItem($ta);

        $te = new TextAreaInputGUI($this->parent_gui->txt('question'), 'question');
        $te->addPlugin('latex');
        $te->addButton('latex');
        $te->addButton('pastelatex');
        $te->setRequired(true);
        $te->setRTESupport(ilObject::_lookupObjId($_GET['ref_id']), "dcl", ilLiveVotingPlugin::PLUGIN_ID, null, false); // We have to prepend that this is a datacollection
        $te->setUseRte(true);
        $te->setRteTags(array(
            'p',
            'a',
            'br',
            'strong',
            'b',
            'i',
            'em',
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
        ));

        $te->setRows(5);
        $this->addItem($te);

        // Columns
        if (static::USE_F_COLUMNS) {
            $columns = new ilSelectInputGUI($this->txt(self::F_COLUMNS), self::F_COLUMNS);
            $columns->setOptions(range(1, 4));
            $this->addItem($columns);
        }

        $xlvoSingleVoteSubFormGUI = xlvoSubFormGUI::getInstance($this->getVoting());
        $xlvoSingleVoteSubFormGUI->appedElementsToForm($this);
        $xlvoSingleVoteSubFormGUI->addJsAndCss(self::dic()->ui()->mainTemplate());
    }


    /**
     * @param string $key
     *
     * @return string
     */
    protected function txt($key)
    {
        return $this->parent_gui->txt($key);
    }


    /**
     *
     */
    public function fillForm()
    {
        $array = array(
            'title'         => $this->voting->getTitle(),
            'description'   => $this->voting->getDescription(),
            'question'      => $this->voting->getQuestionForEditor(),
            'voting_status' => ($this->voting->getVotingStatus() == xlvoVoting::STAT_ACTIVE)
        );
        if ($this->is_new) {
            $array['type'] = $this->voting->getVotingType();
            $array['voting_type'] = $this->voting->getVotingType();
        }
        if (static::USE_F_COLUMNS) {
            $array[self::F_COLUMNS] = ($this->voting->getColumns() - 1);
        }

        $array = xlvoSubFormGUI::getInstance($this->getVoting())->appendValues($array);

        $this->setValuesByArray($array);
        if ($this->voting->getVotingStatus() == xlvoVoting::STAT_INCOMPLETE) {
            ilUtil::sendInfo($this->parent_gui->txt('msg_voting_not_complete'), false);
        }
    }


    /**
     * @return bool
     * @throws ilException
     */
    public function fillObject()
    {
        if (!$this->checkInput()) {
            return false;
        }

        if ($this->is_new) {
            $this->voting->setVotingType($this->getInput('type'));
        }
        $this->voting->setTitle($this->getInput('title'));
        $this->voting->setDescription($this->getInput('description'));
        $this->voting->setQuestion(ilRTE::_replaceMediaObjectImageSrc($this->getInput('question'), 0));
        $this->voting->setObjId($this->parent_gui->getObjId());
        if (static::USE_F_COLUMNS) {
            $this->voting->setColumns(intval($this->getInput(self::F_COLUMNS)) + 1);
        }

        try {
            xlvoSubFormGUI::getInstance($this->getVoting())->handleAfterSubmit($this);

            return true;
        } catch (xlvoSubFormGUIHandleFieldException $ex) {
            ilUtil::sendFailure($ex->getMessage(), true);

            return false;
        }
    }


    /**
     * @return bool
     * @throws ilException
     */
    public function saveObject()
    {
        if (!$this->fillObject()) {
            return false;
        }

        if ($this->voting->getObjId() == $this->parent_gui->getObjId()) {
            $this->voting->store();
            xlvoSubFormGUI::getInstance($this->getVoting())->handleAfterCreation($this->voting);
        } else {
            ilUtil::sendFailure($this->parent_gui->txt('permission_denied_object'), true);
            self::dic()->ctrl()->redirect($this->parent_gui, xlvoVotingGUI::CMD_STANDARD);
        }

        return true;
    }


    /**
     *
     */
    protected function initButtons()
    {
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
    public function getVoting()
    {
        return $this->voting;
    }


    /**
     * @param xlvoVoting $voting
     */
    public function setVoting($voting)
    {
        $this->voting = $voting;
    }
}
