<?php

namespace LiveVoting\Context;

use ilLiveVotingPlugin;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoRbacReview
 *
 * This mocks ilRbacReview in PIN Context (bc of ilObjMediaObject in Text)
 *
 * @package LiveVoting\Context
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoRbacReview
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


    /**
     * @param $a_rol_id
     *
     * @return array
     */
    public function assignedUsers($a_rol_id)
    {
        return array();
    }


    /**
     * @param $user_id
     *
     * @return array
     */
    public function assignedGlobalRoles($user_id)
    {
        return array();
    }


    /**
     * get all assigned roles to a given user
     *
     * @access    public
     *
     * @param integer        usr_id
     *
     * @return    array        all roles (id) the user have
     */
    public function assignedRoles($a_usr_id)
    {
        return [];
    }

    public function isAssignedToAtLeastOneGivenRole(){
        return false;
    }
}
