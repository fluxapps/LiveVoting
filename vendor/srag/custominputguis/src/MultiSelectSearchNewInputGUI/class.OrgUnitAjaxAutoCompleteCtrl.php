<?php

namespace srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI;

require_once __DIR__ . "/../../../../autoload.php";

use ilOrgUnitPathStorage;

/**
 * Class OrgUnitAjaxAutoCompleteCtrl
 *
 * @package srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI
 */
class OrgUnitAjaxAutoCompleteCtrl extends AbstractAjaxAutoCompleteCtrl
{

    /**
     * OrgUnitAjaxAutoCompleteCtrl constructor
     *
     * @param array|null $skip_ids
     */
    public function __construct(/*?*/ array $skip_ids = null)
    {
        parent::__construct($skip_ids);
    }


    /**
     * @inheritDoc
     */
    public function fillOptions(array $ids) : array
    {
        if (!empty($ids)) {
            return $this->skipIds(ilOrgUnitPathStorage::where([
                "ref_id" => $ids
            ])->getArray("ref_id", "path"));
        } else {
            return [];
        }
    }


    /**
     * @inheritDoc
     */
    public function searchOptions(/*?*/ string $search = null) : array
    {
        if (!empty($search)) {
            $where = ilOrgUnitPathStorage::where([
                "path" => "%" . $search . "%"
            ], "LIKE");
        } else {
            $where = ilOrgUnitPathStorage::where([]);
        }

        return $this->skipIds($where->orderBy("path")->getArray("ref_id", "path"));
    }
}
