<?php

namespace LiveVoting\Context\Param;

use Exception;
use ilLiveVotingPlugin;
use srag\DIC\DICStatic;
use srag\DIC\DICTrait;
use ilUIPluginRouterGUI;
use ilObject2;
use LiveVoting\Pin\xlvoPin;

/**
 * Class ParamManager
 *
 * @package LiveVoting\Context\ParamManager
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 *
 */
final class ParamManager {

	use DICTrait;
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


	public function __construct()
	{
		$this->loadBaseClassIfNecessary();
		$this->loadAndPersistAllParams();
	}

	/**
	 * @return ParamManager
	 */
	public static function getInstance() {

		if (! isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function loadAndPersistAllParams() {

		if(null !== trim(filter_input(INPUT_GET, self::PARAM_PIN), "/")) {
			$this->setPin(trim(filter_input(INPUT_GET, self::PARAM_PIN), "/"));
		}

		if(null !== trim(filter_input(INPUT_GET, self::PARAM_REF_ID), "/")) {
			$this->setRefId(trim(filter_input(INPUT_GET, self::PARAM_REF_ID), "/"));
		}

		if(null !== trim(filter_input(INPUT_GET, self::PARAM_PUK), "/")) {
			$this->setPuk(trim(filter_input(INPUT_GET, self::PARAM_PUK), "/"));
		}
		if(null !== trim(filter_input(INPUT_GET, self::PARAM_VOTING), "/")) {
			$this->setVoting(trim(filter_input(INPUT_GET, self::PARAM_VOTING), "/"));
		}
		if(null !== trim(filter_input(INPUT_GET, self::PARAM_PPT), "/")) {
			$this->setPpt(trim(filter_input(INPUT_GET, self::PARAM_PPT), "/"));
		}
	}


	private function loadBaseClassIfNecessary() {
		if($_GET["baseClass"] === null) {
			DICStatic::dic()->ctrl()->initBaseClass(ilUIPluginRouterGUI::class);
		}
	}


	/**
	 * @return int
	 */
	public function getRefId() {
		return $this->ref_id;
	}


	/**
	 * @param int $ref_id
	 */
	public function setRefId($ref_id) {
		$this->ref_id = $ref_id;

		DICStatic::dic()->ctrl()->setParameterByClass(self::PARAM_BASE_CLASS_NAME, self::PARAM_REF_ID, $ref_id);
	}


	/**
	 * @return string
	 */
	public function getPin() {
		return $this->pin;
	}


	/**
	 * @param string $pin
	 */
	public function setPin($pin) {
		$this->pin = $pin;

		DICStatic::dic()->ctrl()->setParameterByClass(self::PARAM_BASE_CLASS_NAME, self::PARAM_PIN, $pin);
	}


	/**
	 * @return string
	 */
	public function getPuk() {
		return $this->puk;
	}


	/**
	 * @param string $puk
	 */
	public function setPuk($puk) {
		$this->puk = $puk;

		DICStatic::dic()->ctrl()->setParameterByClass(self::PARAM_BASE_CLASS_NAME, self::PARAM_PUK, $puk);
	}


	/**
	 * @return int
	 */
	public function getVoting() {
		return $this->voting;
	}


	/**
	 * @param int $voting
	 */
	public function setVoting($voting) {
		$this->voting = $voting;
		DICStatic::dic()->ctrl()->setParameterByClass(self::PARAM_BASE_CLASS_NAME, self::PARAM_VOTING, $voting);
	}


	/**
	 * @return bool
	 */
	public function isPpt() {
		return $this->ppt;
	}


	/**
	 * @param bool $ppt
	 */
	public function setPpt($ppt) {
		$this->ppt = $ppt;

		DICStatic::dic()->ctrl()->setParameterByClass(self::PARAM_BASE_CLASS_NAME, self::PARAM_PPT, $ppt);
	}
}
