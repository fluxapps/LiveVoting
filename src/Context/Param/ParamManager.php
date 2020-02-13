<?php

namespace LiveVoting\Context\Param;

use ilLiveVotingPlugin;
use ilObject;
use ilUIPluginRouterGUI;
use LiveVoting\Pin\xlvoPin;
use LiveVoting\Utils\LiveVotingTrait;
use LiveVoting\Voting\xlvoVotingManager2;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class ParamManager
 *
 * @package LiveVoting\Context\Param
 *
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
final class ParamManager
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    const PARAM_BASE_CLASS_NAME = ilUIPluginRouterGUI::class;
    const PARAM_REF_ID = 'ref_id';
    const PARAM_PIN = 'xlvo_pin';
    const PARAM_PUK = 'xlvo_puk';
    const PARAM_VOTING = 'xlvo_voting';
    const PARAM_PPT = 'xlvo_ppt';
    const PPT_START = 'ppt_start';
    /**
     * @var ParamManager
     */
    protected static $instance;
    /**
     * @var xlvoVotingManager2
     */
    protected static $instance_voting_manager2;
    /**
     * @var int
     */
    protected $ref_id;
    /**
     * @var string
     */
    protected $pin = '';
    /**
     * @var string
     */
    protected $puk = '';
    /**
     * @var int
     */
    protected $voting = 0;
    /**
     * @var bool
     */
    protected $ppt = false;


    /**
     * ParamManager constructor
     */
    public function __construct()
    {
        $this->loadBaseClassIfNecessary();

        $this->loadAndPersistAllParams();
    }


    /**
     * @return self
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     *
     */
    private function loadAndPersistAllParams()
    {
        $pin = trim(filter_input(INPUT_GET, self::PARAM_PIN), "/");
        if (!empty($pin)) {
            $this->setPin($pin);
        }

        $ref_id = trim(filter_input(INPUT_GET, self::PARAM_REF_ID), "/");
        if (!empty($ref_id)) {
            $this->setRefId($ref_id);
        }

        $puk = trim(filter_input(INPUT_GET, self::PARAM_PUK), "/");
        if (!empty($puk)) {
            $this->setPuk($puk);
        }

        $voting = trim(filter_input(INPUT_GET, self::PARAM_VOTING), "/");
        if (!empty($voting)) {
            $this->setVoting($voting);
        }

        $ppt = trim(filter_input(INPUT_GET, self::PARAM_PPT), "/");
        if (!empty($ppt)) {
            $this->setPpt($ppt);
        }
    }


    /**
     *
     */
    private function loadBaseClassIfNecessary()
    {
        $baseClass = filter_input(INPUT_GET, "baseClass");

        if (empty($baseClass)) {
            self::dic()->ctrl()->initBaseClass(ilUIPluginRouterGUI::class);
        }
    }


    /**
     * @return int
     */
    public function getRefId()
    {
        $ref_id = trim(filter_input(INPUT_GET, self::PARAM_REF_ID), "/");

        if (!empty($ref_id)) {
            $this->ref_id = $ref_id;
        }

        if (empty($this->ref_id)) {
            $obj_id = xlvoPin::checkPinAndGetObjId($this->pin, false);

            $this->ref_id = current(ilObject::_getAllReferences($obj_id));
        }

        return $this->ref_id;
    }


    /**
     * @param int $ref_id
     */
    public function setRefId($ref_id)
    {
        $this->ref_id = $ref_id;

        self::dic()->ctrl()->setParameterByClass(self::PARAM_BASE_CLASS_NAME, self::PARAM_REF_ID, $ref_id);
    }


    /**
     * @return string
     */
    public function getPin()
    {
        return $this->pin;
    }


    /**
     * @param string $pin
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        self::dic()->ctrl()->setParameterByClass(self::PARAM_BASE_CLASS_NAME, self::PARAM_PIN, $pin);
    }


    /**
     * @return string
     */
    public function getPuk()
    {
        return $this->puk;
    }


    /**
     * @param string $puk
     */
    public function setPuk($puk)
    {
        $this->puk = $puk;

        self::dic()->ctrl()->setParameterByClass(self::PARAM_BASE_CLASS_NAME, self::PARAM_PUK, $puk);
    }


    /**
     * @return int
     */
    public function getVoting()
    {
        return $this->voting;
    }


    /**
     * @param int $voting
     */
    public function setVoting($voting)
    {
        $this->voting = $voting;

        self::dic()->ctrl()->setParameterByClass(self::PARAM_BASE_CLASS_NAME, self::PARAM_VOTING, $voting);
    }


    /**
     * @return bool
     */
    public function isPpt()
    {
        return $this->ppt;
    }


    /**
     * @param bool $ppt
     */
    public function setPpt($ppt)
    {
        $this->ppt = $ppt;

        self::dic()->ctrl()->setParameterByClass(self::PARAM_BASE_CLASS_NAME, self::PARAM_PPT, $ppt);
    }
}
