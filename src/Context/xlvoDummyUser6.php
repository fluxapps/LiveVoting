<?php

namespace LiveVoting\Context;

use srag\DIC\LiveVoting\DICTrait;
use LiveVoting\Utils\LiveVotingTrait;
use ilLiveVotingPlugin;

/**
 * Class xlvoDummyUser6
 * @package LiveVoting\Context
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class xlvoDummyUser6 implements xlvoDummyUser
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;

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
        return self::dic()->language()->getLangKey();
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