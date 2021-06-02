<?php

namespace srag\ActiveRecordConfig\LiveVoting;

use ActiveRecord;
use ilObject;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\ObjectPropertyFormGUI;

/**
 * Class ActiveRecordObjectFormGUI
 *
 * @package    srag\ActiveRecordConfig\LiveVoting
 *
 * @deprecated Please use PropertyFormGUI from CustomInputGUIs instead
 */
abstract class ActiveRecordObjectFormGUI extends ObjectPropertyFormGUI
{

    /**
     * @var string
     *
     * @deprecated
     */
    const LANG_MODULE = ActiveRecordConfigGUI::LANG_MODULE_CONFIG;
    /**
     * @var string
     *
     * @deprecated
     */
    protected $tab_id;


    /**
     * ActiveRecordObjectFormGUI constructor
     *
     * @param ActiveRecordConfigGUI             $parent
     * @param string                            $tab_id
     * @param ilObject|ActiveRecord|object|null $object
     * @param bool                              $object_auto_store
     *
     * @deprecated
     */
    public function __construct(ActiveRecordConfigGUI $parent, string $tab_id, $object = null, bool $object_auto_store = true)
    {
        $this->tab_id = $tab_id;

        parent::__construct($parent, $object, $object_auto_store);
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ActiveRecordConfigGUI::CMD_UPDATE_CONFIGURE . "_" . $this->tab_id, $this->txt("save"));
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt($this->tab_id));
    }
}
