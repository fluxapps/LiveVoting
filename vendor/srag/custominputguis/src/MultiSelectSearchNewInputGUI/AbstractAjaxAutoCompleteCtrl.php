<?php

namespace srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI;

use srag\DIC\LiveVoting\DICTrait;

/**
 * Class AbstractAjaxAutoCompleteCtrl
 *
 * @package srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractAjaxAutoCompleteCtrl
{

    use DICTrait;

    const CMD_AJAX_AUTO_COMPLETE = "ajaxAutoComplete";


    /**
     * AbstractAjaxAutoCompleteCtrl constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*:void*/
    {
        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_AJAX_AUTO_COMPLETE:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    protected function ajaxAutoComplete()/*:void*/
    {
        $search = strval(filter_input(INPUT_GET, "term"));

        $options = [];

        foreach ($this->searchOptions($search) as $id => $title) {
            $options[] = [
                "id"   => $id,
                "text" => $title
            ];
        }

        self::output()->outputJSON(["results" => $options]);
    }


    /**
     * @param array $ids
     *
     * @return bool
     */
    public function validateOptions(array $ids) : bool
    {
        return (count($ids) === count($this->fillOptions($ids)));
    }


    /**
     * @param string|null $search
     *
     * @return array
     */
    public abstract function searchOptions(/*?*/ string $search = null) : array;


    /**
     * @param array $ids
     *
     * @return array
     */
    public abstract function fillOptions(array $ids) : array;
}
