<?php

namespace srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI;

require_once __DIR__ . "/../../../../autoload.php";

use ilDBConstants;

/**
 * Class ObjectsAjaxAutoCompleteCtrl
 *
 * @package srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI
 */
class ObjectsAjaxAutoCompleteCtrl extends AbstractAjaxAutoCompleteCtrl
{

    /**
     * @var bool
     */
    protected $ref_id;
    /**
     * @var string
     */
    protected $type;


    /**
     * ObjectsAjaxAutoCompleteCtrl constructor
     *
     * @param string     $type
     * @param bool       $ref_id
     *
     * @param array|null $skip_ids
     */
    public function __construct(string $type, bool $ref_id = false,/*?*/ array $skip_ids = null)
    {
        parent::__construct($skip_ids);

        $this->type = $type;
        $this->ref_id = $ref_id;
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
     * @inheritDoc
     */
    public function searchOptions(/*?*/ string $search = null) : array
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

        return $this->skipIds($formatted_objects);
    }
}
