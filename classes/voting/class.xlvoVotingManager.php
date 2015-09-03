<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingInterface.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVote.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoting.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoPlayer.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingType.php');
require_once('./Services/Object/classes/class.ilObject2.php');

/**
 * Class xlvoVotingManager
 */
class xlvoVotingManager implements xlvoVotingInterface {

	const NEW_VOTE = 0;
	/**
	 * @var int
	 */
	protected $obj_id;
	/**
	 * @var ilObjUser
	 */
	protected $user_ilias;


	/**
	 *
	 */
	public function __construct() {
		global $ilUser;

		/**
		 * @var ilUser $ilUser
		 */
		$this->user_ilias = $ilUser;
		$this->obj_id = ilObject2::_lookupObjId($_GET['ref_id']);
	}


	public function getVotings($obj_id = NULL, $filter_active = false) {
		$obj_id = $obj_id ? $obj_id : $this->obj_id;
		$xlvoVotings = xlvoVoting::where(array( 'obj_id' => $obj_id ));

		if ($filter_active == true) {
			$xlvoVotings = $xlvoVotings->where(array( 'voting_status' => xlvoVoting::STAT_ACTIVE ));
		}

		return $xlvoVotings;
	}


	/**
	 * @param $id
	 *
	 * @return xlvoVoting
	 */
	public function getVoting($id) {
		$xlvoVoting = xlvoVoting::find($id);

		if ($xlvoVoting instanceof xlvoVoting) {
			return $xlvoVoting;
		} else {
			return new xlvoVoting();
		}
	}


	public function getOptions($voting_id) {
		$xlvoOptions = xlvoOption::where(array( 'voting_id' => $voting_id ));

		return $xlvoOptions;
	}


	/**
	 * @param $option_id
	 *
	 * @return xlvoOption
	 */
	public function getOption($option_id) {
		$xlvoOption = xlvoOption::find($option_id);

		return $xlvoOption;
	}


	/**
	 * @param            $voting_id
	 * @param null       $option_id
	 * @param bool|false $active_user
	 *
	 * @return $this|ActiveRecordList
	 * @throws Exception
	 */
	public function getVotes($voting_id, $option_id = NULL, $active_user = false) {
		$xlvoVotes = xlvoVote::where(array( 'voting_id' => $voting_id ));
		if ($option_id != NULL) {
			$xlvoVotes = $xlvoVotes->where(array( 'option_id' => $option_id ));
		}
		// USE getVotesOfUser
		if ($active_user) {
			/**
			 * @var $xlvoVoting xlvoVoting
			 */
			$xlvoVoting = xlvoVoting::find($voting_id);
			$xlvoConfig = $this->getVotingConfig($xlvoVoting->getObjId());

			if ($xlvoConfig->isAnonymous()) {
				$xlvoVotes = $xlvoVotes->where(array( 'user_identifier' => session_id() ));
			} else {
				$xlvoVotes = $xlvoVotes->where(array( 'user_id' => $this->user_ilias->getId() ));
			}
		}

		return $xlvoVotes;
	}


	/**
	 * @param      $voting_id
	 * @param null $option_id
	 *
	 * @return ActiveRecordList
	 * @throws Exception
	 */
	public function getVotesOfUser($voting_id, $option_id = NULL) {
		$xlvoVotes = $this->getVotes($voting_id, $option_id);

		/**
		 * @var $xlvoVoting xlvoVoting
		 */
		$xlvoVoting = xlvoVoting::find($voting_id);
		$xlvoConfig = $this->getVotingConfig($xlvoVoting->getObjId());

		if ($xlvoConfig->isAnonymous()) {
			$xlvoVotes = $xlvoVotes->where(array( 'user_identifier' => session_id() ));
		} else {
			$xlvoVotes = $xlvoVotes->where(array( 'user_id' => $this->user_ilias->getId() ));
		}

		return $xlvoVotes;
	}


	/**
	 * @param $vote_id
	 *
	 * @return xlvoVote
	 */
	public function getVote($vote_id) {
		$xlvoVote = xlvoVote::find($vote_id);

		return $xlvoVote;
	}


	/**
	 * @param null $obj_id
	 *
	 * @return xlvoVotingConfig
	 */
	public function getVotingConfig($obj_id = NULL) {
		$obj_id = $obj_id ? $obj_id : $this->obj_id;
		$xlvoVotingConfig = xlvoVotingConfig::find($obj_id);

		return $xlvoVotingConfig;
	}


	public function updateVotingConfig(xlvoVotingConfig $xlvoVotingConfig) {
		$xlvoVotingConfig->update();

		return $xlvoVotingConfig;
	}


	/**
	 * @return ActiveRecordList
	 */
	public function getVotingConfigs() {
		return xlvoVotingConfig::getCollection();
	}


	/**
	 * @param $obj_id
	 *
	 * @return xlvoPlayer
	 */
	public function getPlayer($obj_id) {
		return xlvoPlayer::where(array( 'obj_id' => $obj_id ))->first();
	}


	public function updatePlayer(xlvoPlayer $xlvoPlayer) {
		$xlvoPlayer->update();

		return $xlvoPlayer;
	}


	/**
	 * @param xlvoVote $vote
	 *
	 * @return bool
	 */
	public function vote(xlvoVote $vote) {
		if ($vote->getOptionId() == NULL) {
			// TODO exception handling
			return NULL;
		}

		/**
		 * @var xlvoOption $xlvoOption
		 */
		$xlvoOption = $this->getOption($vote->getOptionId());
		/**
		 * @var xlvoVoting $xlvoVoting
		 */
		$xlvoVoting = $this->getVoting($xlvoOption->getVotingId());
		/**
		 * @var int $obj_id
		 */
		$obj_id = $xlvoVoting->getObjId();
		/**
		 * @var xlvoVotingConfig $xlvoVotingConfig
		 */
		$xlvoVotingConfig = $this->getVotingConfig($obj_id);
		/**
		 * @var xlvoPlayer $xlvoPlayer
		 */
		$xlvoPlayer = $this->getPlayer($obj_id);
		/**
		 * @var xlvoVote[] $exisiting_votes
		 */
		$existing_votes = $this->getVotes($xlvoOption->getVotingId(), NULL, true)->get();

		if (! $xlvoPlayer->isFrozen() && $xlvoPlayer->getStatus() == xlvoPlayer::STAT_RUNNING && $this->isVotingAvailable($obj_id)) {

			/*
			 * SINGLE VOTE
			 */
			if ($xlvoVoting->getVotingType() == xlvoVotingType::SINGLE_VOTE) {
				if ($xlvoVoting->isMultiSelection()) {
					if ($vote->getId() != self::NEW_VOTE) {
						foreach ($existing_votes as $vo) {
							if ($vote->getId() == $vo->getId()) {
								$vote = $this->deleteVote($vo);
							}
						}
					} else {
						$vote = $this->createVote($xlvoVotingConfig, $xlvoOption, $vote);
					}
				} else {
					if (count($existing_votes) > 0) {
						foreach ($existing_votes as $vo) {
							if ($xlvoOption->getId() == $vo->getOptionId()) {
								$vote = $this->deleteVote($vo);
							} else {
								$vote = $this->updateVote($vo, $vote);
							}
						}
					} else {
						$vote = $this->createVote($xlvoVotingConfig, $xlvoOption, $vote);
					}
				}
			}

			/*
			 * FREE INPUT
			 */
			if ($xlvoVoting->getVotingType() == xlvoVotingType::FREE_INPUT) {

				if ($xlvoVoting->isMultiFreeInput()) {
					if ($vote->getId() != self::NEW_VOTE) {
						foreach ($existing_votes as $vo) {
							if ($vote->getId() == $vo->getId()) {
								$vote = $this->deleteVote($vo);
							}
						}
					} else {
						$vote = $this->createVote($xlvoVotingConfig, $xlvoOption, $vote);
					}
				} else {
					if (count($existing_votes) > 0) {
						foreach ($existing_votes as $vo) {
							if ($xlvoOption->getId() == $vo->getOptionId()) {
								if ($vote->getStatus() == xlvoVote::STAT_INACTIVE) {
									$vote = $this->deleteVote($vote);
								} else {
									$vote = $this->updateVote($vo, $vote);
								}
							}
						}
					} else {
						$vote = $this->createVote($xlvoVotingConfig, $xlvoOption, $vote);
					}
				}
			}

			return $vote;
		} else {
			// TODO exception handling
			return NULL;
		}
	}


	protected function createVote(xlvoVotingConfig $config, xlvoOption $option, xlvoVote $vote) {
		$vote->setOptionId($option->getId());
		$vote->setVotingId($option->getVotingId());
		$vote->setType($option->getType());
		$vote->setStatus(xlvoVote::STAT_ACTIVE);
		$vote->setUserIdType($config->isAnonymous());
		switch ($vote->getUserIdType()) {
			case xlvoVote::USER_ILIAS:
				$vote->setUserId($this->user_ilias->getId());
				break;
			case xlvoVote::USER_ANONYMOUS:
				$vote->setUserIdentifier(session_id());
				break;
		}

		$vote->create();
		$created_vote = $this->getVotes($option->getVotingId(), $option->getId(), true)->last();

		return $created_vote;
	}


	protected function updateVote(xlvoVote $existing_vote, xlvoVote $new_vote) {
		$existing_vote->setFreeInput($new_vote->getFreeInput());
		$existing_vote->setOptionId($new_vote->getOptionId());
		$existing_vote->update();
		$updated_vote = xlvoVote::find($existing_vote->getId());

		return $updated_vote;
	}


	protected function deleteVote(xlvoVote $vote) {
		$vote->delete();
		$deleted_vote = new xlvoVote();
		$deleted_vote->setStatus(xlvoVote::STAT_INACTIVE);
		$deleted_vote->setVotingId($vote->getVotingId());
		$deleted_vote->setOptionId($vote->getOptionId());

		return $deleted_vote;
	}


	public function deleteVotesForOption($option_id) {
		$option = xlvoOption::find($option_id);
		$votes = $this->getVotes($option->getVotingId(), $option_id);

		foreach ($votes->get() as $vote) {
			$vote->delete();
		}

		return true;
	}


	public function deleteVotesForVoting($voting_id) {
		$votes = $this->getVotes($voting_id);
		foreach ($votes->get() as $vote) {
			$vote->delete();
		}

		return true;
	}


	public function deleteVotesForObject($obj_id) {
		$votings = xlvoVoting::where(array( 'obj_id' => $obj_id ));
		foreach ($votings as $voting) {
			$this->deleteVotesForVoting($voting);
		}

		return true;
	}


	public function isVotingAvailable($obj_id) {
		$xlvoVotingConfig = xlvoVotingConfig::find($obj_id);

		$terminable = $xlvoVotingConfig->isTerminable();

		if (! $terminable) {
			return true;
		} else {
			$format = 'Y-m-d H:i:s';
			$start_date = strtotime($xlvoVotingConfig->getStartDate());
			$end_date = strtotime($xlvoVotingConfig->getEndDate());
			$now = strtotime(date($format));
			if ($start_date <= $now && $end_date >= $now) {
				return true;
			} else {
				return false;
			}
		}
	}


	/**
	 * @param $voting_id
	 */
	public function setActiveVoting($voting_id) {
		/**
		 * @var xlvoVoting $xlvoVoting
		 */
		$xlvoVoting = $this->getVoting($voting_id);
		$xlvoPlayer = $this->getPlayer($xlvoVoting->getObjId());
		if ($xlvoPlayer == NULL) {
			$xlvoPlayer = new xlvoPlayer();
			$xlvoPlayer->setObjId($xlvoVoting->getObjId());
			$xlvoPlayer->setActiveVoting($voting_id);
			$xlvoPlayer->setReset(xlvoPlayer::RESET_OFF);
			$xlvoPlayer->setStatus(xlvoPlayer::STAT_START_VOTING);
			$xlvoPlayer->setFrozen(true);
			$xlvoPlayer->create();
		} else {
			$xlvoPlayer->setActiveVoting($voting_id);
			$xlvoPlayer->setStatus(xlvoPlayer::STAT_RUNNING);
			$xlvoPlayer->update();
		}
	}


	/**
	 * @param $obj_id
	 *
	 * @return int
	 */
	public function getActiveVoting($obj_id) {
		/**
		 * @var xlvoPlayer $xlvoPlayer
		 */
		$xlvoPlayer = $this->getPlayer($obj_id);
		if ($xlvoPlayer instanceof xlvoPlayer) {
			return $xlvoPlayer->getActiveVoting();
		} else {
			return 0;
		}
	}


	public function freezeVoting($obj_id) {
		/**
		 * @var xlvoPlayer $xlvoPlayer
		 */
		$xlvoPlayer = $this->getPlayer($obj_id);
		$xlvoPlayer->setFrozen(true);
		$this->updatePlayer($xlvoPlayer);
	}


	public function unfreezeVoting($obj_id) {
		/**
		 * @var xlvoPlayer $xlvoPlayer
		 */
		$xlvoPlayer = $this->getPlayer($obj_id);
		$xlvoPlayer->setFrozen(false);
		$this->updatePlayer($xlvoPlayer);
	}


	public function terminateVoting($obj_id) {
		/**
		 * @var xlvoPlayer $xlvoPlayer
		 */
		$xlvoPlayer = $this->getPlayer($obj_id);
		$this->freezeVoting($obj_id);
		$xlvoPlayer->setStatus(xlvoPlayer::STAT_STOPPED);
		$this->updatePlayer($xlvoPlayer);
	}
}