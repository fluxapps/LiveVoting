<?php

namespace LiveVoting\Voting;

use ilLiveVotingPlugin;
use ilRepositorySelector2InputGUI;
use LiveVoting\Utils\LiveVotingTrait;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\PropertyFormGUI;
use xlvoVotingGUI;

/**
 * Class DuplicateToAnotherObjectSelectFormGUI
 *
 * @package LiveVoting\Voting
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DuplicateToAnotherObjectSelectFormGUI extends PropertyFormGUI
{

    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    const LANG_MODULE = "voting";


    /**
     * DuplicateToAnotherObjectSelectFormGUI constructor
     *
     * @param xlvoVotingGUI $parent
     */
    public function __construct(xlvoVotingGUI $parent)
    {
        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return null;
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(xlvoVotingGUI::CMD_DUPLICATE_TO_ANOTHER_OBJECT, $this->txt("duplicate"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->addItem($this->getRepositorySelector());
    }


    /**
     * @return ilRepositorySelector2InputGUI
     */
    public function getRepositorySelector()
    {
        $repository_selector = new ilRepositorySelector2InputGUI(self::plugin()->translate("obj_xlvo"), "ref_id");

        $repository_selector->setRequired(true);

        $repository_selector->getExplorerGUI()->setSelectableTypes([ilLiveVotingPlugin::PLUGIN_ID]);

        return $repository_selector;
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt(xlvoVotingGUI::CMD_DUPLICATE_TO_ANOTHER_OBJECT));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            default:
                break;
        }
    }
}
