<?php

namespace LiveVoting\User;

use ilLiveVotingPlugin;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoParticipant
 *
 * @package LiveVoting\User
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 */
class xlvoParticipant {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	/**
	 * @var int
	 */
	protected $user_id;
	/**
	 * @var string
	 */
	protected $user_identifier;
	/**
	 * @var int
	 */
	protected $user_id_type;
	/**
	 * @var int
	 */
	protected $number;


	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}


	/**
	 * @param int $user_id
	 */
	public function setUserId($user_id) {
		$this->user_id = $user_id;
	}


	/**
	 * @return string
	 */
	public function getUserIdentifier() {
		return $this->user_identifier;
	}


	/**
	 * @param string $user_identifier
	 */
	public function setUserIdentifier($user_identifier) {
		$this->user_identifier = $user_identifier;
	}


	/**
	 * @return int
	 */
	public function getUserIdType() {
		return $this->user_id_type;
	}


	/**
	 * @param int $user_id_type
	 */
	public function setUserIdType($user_id_type) {
		$this->user_id_type = $user_id_type;
	}


	/**
	 * @return int
	 */
	public function getNumber() {
		return $this->number;
	}


	/**
	 * @param int $number
	 */
	public function setNumber($number) {
		$this->number = $number;
	}
}
