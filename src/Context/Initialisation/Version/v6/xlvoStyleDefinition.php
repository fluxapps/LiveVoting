<?php

namespace LiveVoting\Context\Initialisation\Version\v6;

use ilLiveVotingPlugin;
use ilSkinStyleXML;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoStyleDefinition
 *
 * @package LiveVoting\Context\Initialisation\Version\v6
 */
class xlvoStyleDefinition
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    /**
     * @var xlvoSkin
     */
    protected $skin;


    /**
     * xlvoStyleDefinition constructor.
     */
    public function __construct()
    {
        $this->skin = new xlvoSkin();
    }


    /**
     * @return string
     */
    public function getSkin()
    {
        return $this->skin;
    }


    /**
     * @return string
     */
    public function getImageDirectory($style_id)
    {
        return '';
    }
}

/**
 * Class xlvoSkin
 *
 * @package LiveVoting\Context\Initialisation\Version\v6
 */
class xlvoSkin
{

    use DICTrait;


    /**
     * @return string
     */
    public function getId()
    {
        return 'delos';
    }


    /**
     * @return bool
     */
    public function hasStyle()
    {
        return false;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'Delos';
    }


    /**
     * @return ilSkinStyleXML
     */
    public function getDefaultStyle()
    {
        // required with ilias 5.4
        return new ilSkinStyleXML($this->getId(), $this->getName());
    }
}
