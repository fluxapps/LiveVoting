<?php

namespace LiveVoting\Utils;

use LiveVoting\Access\Access;
use LiveVoting\Access\Ilias;

/**
 * Trait LiveVotingTrait
 *
 * @package LiveVoting\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait LiveVotingTrait
{

    /**
     * @return Access
     */
    protected static function access()/*: Access*/
    {
        return Access::getInstance();
    }


    /**
     * @return Ilias
     */
    protected static function ilias()/*: Ilias*/
    {
        return Ilias::getInstance();
    }
}
