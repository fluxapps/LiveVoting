<?php

namespace LiveVoting\Context;

use ilLiveVotingPlugin;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;
use ilObjUser;

/**
 * Class xlvoDummyUser
 * Dummy user which only simulates required functionally for the ilHelpGUI class.
 *
 * @package LiveVoting\Context
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class xlvoDummyUser54 extends ilObjUser implements xlvoDummyUser
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    const LANGUAGE_CODE = "de";

    /**
     * xlvoDummyUser constructor.
     */
    public function __construct()
    {
    }

    /**
     * Returns the language of the user.
     * This dummy only returns statically the "de" language code
     * because no other help packages are available atm. (27.10.2016)
     *
     * @return string returns the language code "de" without the quotes.
     */
    public function getLanguage()
    {
        return self::LANGUAGE_CODE;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return 13;
    }


    /**
     * This dummy method returns statically false.
     *
     * @param string $preference Preference name which will be ignored by this dummy function.
     *
     * @return bool         Returns constant false.
     */
    public function getPref($preference)
    {
        return false;
    }
}
