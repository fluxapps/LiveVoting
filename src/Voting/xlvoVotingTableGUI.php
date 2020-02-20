<?php

namespace LiveVoting\Voting;

use ilAdvancedSelectionListGUI;
use ilCheckboxInputGUI;
use ilFormPropertyGUI;
use ilLiveVotingPlugin;
use ilObjLiveVotingAccess;
use ilSelectInputGUI;
use ilTable2GUI;
use ilUtil;
use LiveVoting\Js\xlvoJs;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use LiveVoting\Utils\LiveVotingTrait;
use srag\CustomInputGUIs\LiveVoting\TextInputGUI\TextInputGUI;
use srag\DIC\LiveVoting\DICTrait;
use xlvoVotingGUI;

/**
 * Class xlvoVotingTableGUI
 *
 * @package LiveVoting\Voting
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoVotingTableGUI extends ilTable2GUI
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    const TBL_ID = 'tbl_xlvo';
    const LENGTH = 100;
    /**
     * @var xlvoVotingGUI
     */
    protected $voting_gui;
    /**
     * @var array
     */
    protected $filter = array();
    /**
     * @var ilObjLiveVotingAccess
     */
    protected $access;


    public function __construct(xlvoVotingGUI $a_parent_obj, $a_parent_cmd)
    {
        $this->voting_gui = $a_parent_obj;
        $this->access = new ilObjLiveVotingAccess();

        xlvoJs::getInstance()->addLibToHeader('sortable.min.js');

        $this->setId(self::TBL_ID);
        $this->setPrefix(self::TBL_ID);
        $this->setFormName(self::TBL_ID);
        self::dic()->ctrl()->saveParameter($a_parent_obj, $this->getNavParameter());

        parent::__construct($a_parent_obj, $a_parent_cmd);

        $this->setRowTemplate('tpl.tbl_voting.html', self::plugin()->directory());
        $this->setExternalSorting(true);
        $this->setExternalSegmentation(true);
        $this->initColums();
        $this->addFilterItems();
        $this->parseData();

        $this->setFormAction(self::dic()->ctrl()->getFormAction($a_parent_obj));
        $this->addCommandButton('saveSorting', $this->txt('save_sorting'));
    }


    /**
     * @param string $key
     *
     * @return string
     */
    protected function txt($key)
    {
        return $this->voting_gui->txt($key);
    }


    protected function addFilterItems()
    {
        $title = new TextInputGUI($this->txt('title'), 'title');
        $this->addAndReadFilterItem($title);

        $question = new TextInputGUI($this->txt('question'), 'question');
        $this->addAndReadFilterItem($question);

        $status = new ilSelectInputGUI($this->txt('status'), 'voting_status');
        $status_options = array(
            -1                          => '',
            xlvoVoting::STAT_INACTIVE   => $this->txt('status_' . xlvoVoting::STAT_INACTIVE),
            xlvoVoting::STAT_ACTIVE     => $this->txt('status_' . xlvoVoting::STAT_ACTIVE),
            xlvoVoting::STAT_INCOMPLETE => $this->txt('status_' . xlvoVoting::STAT_INCOMPLETE),
        );
        $status->setOptions($status_options);
        //		$this->addAndReadFilterItem($status); deativated at the moment

        $type = new ilSelectInputGUI($this->txt('type'), 'voting_type');
        $type_options = array(
            -1 => '',
        );

        foreach (xlvoQuestionTypes::getActiveTypes() as $qtype) {
            $type_options[$qtype] = $this->txt('type_' . $qtype);
        }

        $type->setOptions($type_options);
        $this->addAndReadFilterItem($type);
    }


    /**
     * @param ilFormPropertyGUI $item
     */
    protected function addAndReadFilterItem(ilFormPropertyGUI $item)
    {
        $this->addFilterItem($item);
        $item->readFromSession();
        if ($item instanceof ilCheckboxInputGUI) {
            $this->filter[$item->getPostVar()] = $item->getChecked();
        } else {
            $this->filter[$item->getPostVar()] = $item->getValue();
        }
    }


    /**
     * @param array $a_set
     */
    public function fillRow($a_set)
    {
        /**
         * @var xlvoVoting $xlvoVoting
         */
        $xlvoVoting = xlvoVoting::find($a_set['id']);
        $this->tpl->setVariable('TITLE', $this->shorten($xlvoVoting->getTitle()));
        $this->tpl->setVariable('DESCRIPTION', $this->shorten($xlvoVoting->getDescription()));

        $question = strip_tags($xlvoVoting->getQuestion());

        $question = $this->shorten($question);
        $this->tpl->setVariable('QUESTION', ilUtil::prepareTextareaOutput($question, true));
        $this->tpl->setVariable('TYPE', $this->txt('type_' . $xlvoVoting->getVotingType()));

        $voting_status = $this->getVotingStatus($xlvoVoting->getVotingStatus());
        //		$this->tpl->setVariable('STATUS', $voting_status); // deactivated at the moment

        $this->tpl->setVariable('ID', $xlvoVoting->getId());

        $this->addActionMenu($xlvoVoting);
    }


    protected function initColums()
    {
        $this->addColumn('', 'position', '20px');
        $this->addColumn($this->txt('title'));
        $this->addColumn($this->txt('question'));
        $this->addColumn($this->txt('type'));
        //		$this->addColumn($this->txt('status'));
        $this->addColumn($this->txt('actions'), '', '150px');
    }


    /**
     * @param xlvoVoting $xlvoVoting
     */
    protected function addActionMenu(xlvoVoting $xlvoVoting)
    {
        $current_selection_list = new ilAdvancedSelectionListGUI();
        $current_selection_list->setListTitle($this->txt('actions'));
        $current_selection_list->setId('xlvo_actions_' . $xlvoVoting->getId());
        $current_selection_list->setUseImages(false);

        self::dic()->ctrl()->setParameter($this->voting_gui, xlvoVotingGUI::IDENTIFIER, $xlvoVoting->getId());
        if ($this->access->hasWriteAccess()) {
            $current_selection_list->addItem($this->txt('edit'), xlvoVotingGUI::CMD_EDIT, self::dic()->ctrl()
                ->getLinkTarget($this->voting_gui, xlvoVotingGUI::CMD_EDIT));
            $current_selection_list->addItem($this->txt('reset'), xlvoVotingGUI::CMD_CONFIRM_RESET, self::dic()->ctrl()
                ->getLinkTarget($this->voting_gui, xlvoVotingGUI::CMD_CONFIRM_RESET));
            $current_selection_list->addItem($this->txt(xlvoVotingGUI::CMD_DUPLICATE), xlvoVotingGUI::CMD_DUPLICATE, self::dic()->ctrl()
                ->getLinkTarget($this->voting_gui, xlvoVotingGUI::CMD_DUPLICATE));
            $current_selection_list->addItem($this->txt(xlvoVotingGUI::CMD_DUPLICATE_TO_ANOTHER_OBJECT), xlvoVotingGUI::CMD_DUPLICATE_TO_ANOTHER_OBJECT_SELECT, self::dic()->ctrl()
                ->getLinkTarget($this->voting_gui, xlvoVotingGUI::CMD_DUPLICATE_TO_ANOTHER_OBJECT_SELECT));
            $current_selection_list->addItem($this->txt('delete'), xlvoVotingGUI::CMD_CONFIRM_DELETE, self::dic()->ctrl()
                ->getLinkTarget($this->voting_gui, xlvoVotingGUI::CMD_CONFIRM_DELETE));
        }
        $current_selection_list->getHTML();
        $this->tpl->setVariable('ACTIONS', $current_selection_list->getHTML());
    }


    protected function parseData()
    {
        // Filtern
        $this->determineOffsetAndOrder();
        $this->determineLimit();

        $collection = xlvoVoting::where(array('obj_id' => $this->voting_gui->getObjId()))
            ->where(array('voting_type' => xlvoQuestionTypes::getActiveTypes()))->orderBy('position', 'ASC');
        $this->setMaxCount($collection->count());
        $sorting_column = $this->getOrderField() ? $this->getOrderField() : 'position';
        $offset = $this->getOffset() ? $this->getOffset() : 0;

        $sorting_direction = $this->getOrderDirection();
        $num = $this->getLimit();

        $collection->orderBy($sorting_column, $sorting_direction);
        $collection->limit($offset, $num);

        foreach ($this->filter as $filter_key => $filter_value) {
            switch ($filter_key) {
                case 'title':
                case 'question':
                    if ($filter_value) {
                        $collection = $collection->where(array($filter_key => '%' . $filter_value . '%'), 'LIKE');
                    }
                    break;
                case 'voting_status':

                case 'voting_type':
                    if ($filter_value > -1) {
                        $collection = $collection->where(array($filter_key => $filter_value));
                    }
                    break;
            }
        }
        $this->setData($collection->getArray());
    }


    /**
     * @param $voting_status
     *
     * @return string
     */
    protected function getVotingStatus($voting_status)
    {
        return $this->txt('status_' . $voting_status);
    }


    /**
     * @param string $question
     *
     * @return string
     */
    protected function shorten($question)
    {
        return strlen($question) > self::LENGTH ? substr($question, 0, self::LENGTH) . "..." : $question;
    }
}
