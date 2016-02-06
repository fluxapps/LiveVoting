<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Vote/class.xlvoVote.php');

/**
 * Class xlvoVotingManager2
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoVotingManager2 {

	/**
	 * @var xlvoPlayer
	 */
	protected $player;
	/**
	 * @var xlvoVoting
	 */
	protected $voting;
	/**
	 * @var int
	 */
	protected $obj_id = 0;


	/**
	 * xlvoVotingManager2 constructor.
	 * @param $pin
	 */
	public function __construct($pin) {
		$this->obj_id = xlvoPin::checkPin($pin);
		$this->player = xlvoPlayer::getInstanceForObjId($this->obj_id);
		$this->voting = xlvoVoting::findOrGetInstance($this->getPlayer()->getActiveVoting());
	}


	/**
	 * @param null $option
	 */
	public function vote($option = null) {
		$xlvoOption = xlvoOption::findOrGetInstance($option);
		if ($this->hasUserVotedForOption($xlvoOption)) {
			$this->unvote($option);
		} else {
			xlvoVote::vote(xlvoUser::getInstance(), $this->getVoting()->getId(), $option);
		}
		if ($this->getVoting()->isMultiSelection()) {
			$this->unvoteAll($option);
		}
	}


	/**
	 * @param null $option
	 */
	public function unvote($option = null) {
		xlvoVote::unvote(xlvoUser::getInstance(), $this->getVoting()->getId(), $option);
	}


	/**
	 * @param null $except_option_id
	 */
	public function unvoteAll($except_option_id = null) {
		foreach ($this->getVotesOfUser() as $xlvoVote) {
			if ($except_option_id && $xlvoVote->getId() == $except_option_id) {
				continue;
			}
			$xlvoVote->setStatus(xlvoVote::STAT_INACTIVE);
			$xlvoVote->store();
		}
	}


	/**
	 * @return xlvoVote[]
	 */
	public function getVotesOfUser() {
		return xlvoVote::getVotesOfUser(xlvoUser::getInstance(), $this->getVoting()->getId());
	}


	/**
	 * @param xlvoOption $xlvoOption
	 * @return bool
	 */
	public function hasUserVotedForOption(xlvoOption $xlvoOption) {
		$options = array();
		foreach ($this->getVotesOfUser() as $xlvoVote) {
			$options[] = $xlvoVote->getOptionId();
		}
		return in_array($xlvoOption->getId(), $options);
	}


	/**
	 * @return xlvoOption[]
	 */
	public function getOptions() {
		return $this->voting->getVotingOptions();
	}


	/**
	 * @return xlvoPlayer
	 */
	public function getPlayer() {
		return $this->player;
	}


	/**
	 * @param xlvoPlayer $player
	 */
	public function setPlayer($player) {
		$this->player = $player;
	}


	/**
	 * @return xlvoVoting
	 */
	public function getVoting() {
		return $this->voting;
	}


	/**
	 * @param xlvoVoting $voting
	 */
	public function setVoting($voting) {
		$this->voting = $voting;
	}


	/**
	 * @return int
	 */
	public function getObjId() {
		return $this->obj_id;
	}


	/**
	 * @param int $obj_id
	 */
	public function setObjId($obj_id) {
		$this->obj_id = $obj_id;
	}
}