<?php

namespace srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI;

use srag\DIC\LiveVoting\DICTrait;

/**
 * Class AbstractAjaxAutoCompleteCtrl
 *
 * @package srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI
 */
abstract class AbstractAjaxAutoCompleteCtrl
{

    use DICTrait;

    const CMD_AJAX_AUTO_COMPLETE = "ajaxAutoComplete";
    /**
     * @var array|null
     */
    protected $skip_ids = null;


    /**
     * AbstractAjaxAutoCompleteCtrl constructor
     *
     * @param array|null $skip_ids
     */
    public function __construct(/*?*/ array $skip_ids = null)
    {
        $this->skip_ids = $skip_ids;
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
     * @param array $ids
     *
     * @return array
     */
    public abstract function fillOptions(array $ids) : array;


    /**
     * @param string|null $search
     *
     * @return array
     */
    public abstract function searchOptions(/*?*/ string $search = null) : array;


    /**
     * @param array $ids
     *
     * @return bool
     */
    public function validateOptions(array $ids) : bool
    {
        return (count($this->skipIds($ids)) === count($this->fillOptions($ids)));
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
     * @return array
     */
    protected function skipIds(array $ids) : array
    {
        if (empty($this->skip_ids)) {
            return $ids;
        }

        return array_filter($ids, function ($id) : bool {
            return (!in_array($id, $this->skip_ids));
        }, ARRAY_FILTER_USE_KEY);
    }
}
