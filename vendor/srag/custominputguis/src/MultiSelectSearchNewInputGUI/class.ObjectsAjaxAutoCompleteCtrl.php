<?php

namespace srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI;

use ilDBConstants;

/**
 * Class ObjectsAjaxAutoCompleteCtrl
 *
 * @package srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ObjectsAjaxAutoCompleteCtrl extends AbstractAjaxAutoCompleteCtrl
{

    /**
     * @var string
     */
    protected $type;
    /**
     * @var bool
     */
    protected $ref_id;


    /**
     * ObjectsAjaxAutoCompleteCtrl constructor
     *
     * @param string $type
     * @param bool   $ref_id
     */
    public function __construct(string $type, bool $ref_id = false)
    {
        parent::__construct();

        $this->type = $type;
        $this->ref_id = $ref_id;
    }


    /**
     * @inheritDoc
     */
    public function searchOptions(string $search = null) : array
    {
        $result = self::dic()->database()->queryF('
SELECT ' . ($this->ref_id ? 'object_reference.ref_id' : 'object_data.obj_id') . ', title
FROM object_data
INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id
WHERE type=%s
AND object_reference.deleted IS NULL
' . (!empty($search) ? ' AND ' . self::dic()
                    ->database()
                    ->like("title", ilDBConstants::T_TEXT, '%%' . $search . '%%') : '') . ' ORDER BY title ASC', [ilDBConstants::T_TEXT], [$this->type]);

        return $this->formatObjects(self::dic()->database()->fetchAll($result));
    }


    /**
     * @inheritDoc
     */
    public function fillOptions(array $ids) : array
    {
        $result = self::dic()->database()->queryF('
SELECT ' . ($this->ref_id ? 'object_reference.ref_id' : 'object_data.obj_id') . ', title
FROM object_data
INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id
WHERE type=%s
AND object_reference.deleted IS NULL
AND ' . self::dic()
                ->database()
                ->in(($this->ref_id ? 'object_reference.ref_id' : 'object_data.obj_id'), $ids, false, ilDBConstants::T_INTEGER) . ' ORDER BY title ASC', [ilDBConstants::T_TEXT], [$this->type]);

        return $this->formatObjects(self::dic()->database()->fetchAll($result));
    }


    /**
     * @param array $objects
     *
     * @return array
     */
    protected function formatObjects(array $objects) : array
    {
        $formatted_objects = [];

        foreach ($objects as $object) {
            $formatted_objects[$object[($this->ref_id ? 'ref_id' : 'obj_id')]] = $object["title"];
        }

        return $formatted_objects;
    }
}
