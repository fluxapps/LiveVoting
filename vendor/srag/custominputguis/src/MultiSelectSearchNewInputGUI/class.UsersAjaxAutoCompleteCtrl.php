<?php

namespace srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI;

use ilDBConstants;
use ilObjUser;

/**
 * Class UsersAjaxAutoCompleteCtrl
 *
 * @package srag\CustomInputGUIs\LiveVoting\MultiSelectSearchNewInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UsersAjaxAutoCompleteCtrl extends AbstractAjaxAutoCompleteCtrl
{

    /**
     * UsersAjaxAutoCompleteCtrl constructor
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
        return $this->formatUsers(ilObjUser::searchUsers($search));
    }


    /**
     * @inheritDoc
     */
    public function fillOptions(array $ids) : array
    {
        return $this->formatUsers(self::dic()->database()->fetchAll(self::dic()->database()->queryF('
SELECT usr_id, firstname, lastname, login
FROM usr_data
WHERE active=1
AND usr_id!=%s
AND ' . self::dic()
                ->database()
                ->in("usr_id", $ids, false, ilDBConstants::T_INTEGER), [ilDBConstants::T_INTEGER], [ANONYMOUS_USER_ID])));
    }


    /**
     * @param array $users
     *
     * @return array
     */
    protected function formatUsers(array $users) : array
    {
        $formatted_users = [];

        foreach ($users as $user) {
            $formatted_users[$user["usr_id"]] = $user["firstname"] . " " . $user["lastname"] . " (" . $user["login"] . ")";
        }

        return $formatted_users;
    }
}
