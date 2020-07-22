<?php

namespace srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI;

use ilOrgUnitPathStorage;

/**
 * Class OrgUnitAjaxAutoCompleteCtrl
 *
 * @package srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class OrgUnitAjaxAutoCompleteCtrl extends AbstractAjaxAutoCompleteCtrl
{

    /**
     * OrgUnitAjaxAutoCompleteCtrl constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @inheritDoc
     */
    public function searchOptions(string $search = null) : array
    {
        if (!empty($search)) {
            $where = ilOrgUnitPathStorage::where([
                "path" => "%" . $search . "%"
            ], "LIKE");
        } else {
            $where = ilOrgUnitPathStorage::where([]);
        }

        return $where->orderBy("path")->getArray("ref_id", "path");
    }


    /**
     * @inheritDoc
     */
    public function fillOptions(array $ids) : array
    {
        if (!empty($ids)) {
            return ilOrgUnitPathStorage::where([
                "ref_id" => $ids
            ])->getArray("ref_id", "path");
        } else {
            return [];
        }
    }
}
