<?php

namespace srag\CustomInputGUIs\LiveVoting\LearningProgressPieUI;

use ilLPObjSettings;
use ilLPStatus;
use ilObjectLP;

/**
 * Class UsrIdsLearningProgressPieUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\LearningProgressPieUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UsrIdsLearningProgressPieUI extends AbstractLearningProgressPieUI
{

    /**
     * @var int
     */
    protected $obj_id;
    /**
     * @var int[]
     */
    protected $usr_ids = [];


    /**
     * @param int $obj_id
     *
     * @return self
     */
    public function withObjId(int $obj_id) : self
    {
        $this->obj_id = $obj_id;

        return $this;
    }


    /**
     * @param int[] $usr_ids
     *
     * @return self
     */
    public function withUsrIds(array $usr_ids) : self
    {
        $this->usr_ids = $usr_ids;

        return $this;
    }


    /**
     * @inheritDoc
     */
    protected function parseData() : array
    {
        if (count($this->usr_ids) > 0) {
            return array_reduce($this->usr_ids, function (array $data, int $usr_id) : array {
                $status = $this->getStatus($usr_id);

                if (!isset($data[$status])) {
                    $data[$status] = 0;
                }

                $data[$status]++;

                return $data;
            }, []);
        } else {
            return [];
        }
    }


    /**
     * @inheritDoc
     */
    protected function getCount() : int
    {
        return count($this->usr_ids);
    }


    /**
     * @param int $usr_id
     *
     * @return int
     */
    private function getStatus(int $usr_id) : int
    {
        // Avoid exit
        if (ilObjectLP::getInstance($this->obj_id)->getCurrentMode() != ilLPObjSettings::LP_MODE_UNDEFINED) {
            $status = intval(ilLPStatus::_lookupStatus($this->obj_id, $usr_id));
        } else {
            $status = ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM;
        }

        return $status;
    }
}
