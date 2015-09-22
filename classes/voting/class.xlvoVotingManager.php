<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingInterface.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingManagerException.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVote.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoting.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoPlayer.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingType.php');
require_once('./Services/Object/classes/class.ilObject2.php');

/**
 * Class xlvoVotingManager
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoVotingManager implements xlvoVotingInterface {

	const NEW_VOTE = 0;
	/**
	 * @var ilObjUser
	 */
	protected $user_ilias;
	/**
	 * @var ilObjLiveVotingAccess
	 */
	protected $access;


	public function __construct() {
		global $ilUser;

		/**
		 * @var ilObjUser $ilUser
		 */
		$this->user_ilias = $ilUser;
		$this->access = new ilObjLiveVotingAccess();
	}


	/**
	 * @param $obj_id
	 *
	 * @return xlvoVoting
	 */
	public function getActiveVotings($obj_id) {
		/**
		 * @var xlvoVoting $xlvoVotings
		 */
		$xlvoVotings = xlvoVoting::where(array( 'obj_id' => $obj_id, 'voting_status' => xlvoVoting::STAT_ACTIVE ))->orderBy('position', 'ASC');

		return $xlvoVotings;
	}


	/**
	 * @param $obj_id
	 *
	 * @return xlvoVoting
	 */
	public function getVotings($obj_id) {
		/**
		 * @var xlvoVoting $xlvoVotings
		 */
		$xlvoVotings = xlvoVoting::where(array( 'obj_id' => $obj_id ));

		return $xlvoVotings;
	}


	/**
	 * @param $id
	 *
	 * @return xlvoVoting
	 * @throws xlvoVotingManagerException
	 */
	public function getVoting($id) {
		/**
		 * @var xlvoVoting $xlvoVoting
		 */
		$xlvoVoting = xlvoVoting::find($id);

		if ($xlvoVoting instanceof xlvoVoting) {
			return $xlvoVoting;
		} else {
			throw new xlvoVotingManagerException('Returned object not an instance of xlvoVoting.');
		}
	}


	/**
	 * @param $voting_id
	 *
	 * @return xlvoOption
	 */
	public function getOptionsOfVoting($voting_id) {
		/**
		 * @var xlvoOption $xlvoOptions
		 */
		$xlvoOptions = xlvoOption::where(array( 'voting_id' => $voting_id ));

		return $xlvoOptions;
	}


	/**
	 * @param $option_id
	 *
	 * @return xlvoOption
	 * @throws xlvoVotingManagerException
	 */
	public function getOption($option_id) {
		/**
		 * @var xlvoOption $xlvoOption
		 */
		$xlvoOption = xlvoOption::find($option_id);

		if ($xlvoOption instanceof xlvoOption) {
			return $xlvoOption;
		} else {
			throw new xlvoVotingManagerException('Returned object not an instance of xlvoOption.');
		}
	}


	/**
	 * @param $voting_id
	 *
	 * @return xlvoVote
	 */
	public function getVotesOfVoting($voting_id) {
		/**
		 * @var xlvoVote $xlvoVotes
		 */
		$xlvoVotes = xlvoVote::where(array( 'voting_id' => $voting_id ));

		return $xlvoVotes;
	}


	/**
	 * @param $option_id
	 *
	 * @return xlvoVote
	 */
	public function getVotesOfOption($option_id) {
		/**
		 * @var xlvoVote $xlvoVotes
		 */
		$xlvoVotes = xlvoVote::where(array( 'option_id' => $option_id ));

		return $xlvoVotes;
	}


	/**
	 * @param $voting_id
	 * @param $option_id
	 *
	 * @return xlvoVote
	 */
	public function getVotesOfUserOfOption($voting_id, $option_id) {
		/**
		 * @var xlvoVote $xlvoVotes
		 */
		$xlvoVotes = $this->getVotesOfOption($option_id);

		/**
		 * @var $xlvoVoting xlvoVoting
		 */
		$xlvoVoting = xlvoVoting::find($voting_id);
		/**
		 * @var xlvoVotingConfig $xlvoConfig
		 */
		$xlvoConfig = $this->getVotingConfig($xlvoVoting->getObjId());

		if ($xlvoConfig->isAnonymous()) {
			$xlvoVotes = $xlvoVotes->where(array( 'user_identifier' => session_id() ));
		} else {
			$xlvoVotes = $xlvoVotes->where(array( 'user_id' => $this->user_ilias->getId() ));
		}

		return $xlvoVotes;
	}


	/**
	 * @param $voting_id
	 *
	 * @return xlvoVote
	 */
	public function getVotesOfUserOfVoting($voting_id) {
		/**
		 * @var xlvoVote $xlvoVotes
		 */
		$xlvoVotes = $this->getVotesOfVoting($voting_id);

		/**
		 * @var $xlvoVoting xlvoVoting
		 */
		$xlvoVoting = xlvoVoting::find($voting_id);
		/**
		 * @var xlvoVotingConfig $xlvoConfig
		 */
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
		/**
		 * @var xlvoVote $xlvoVote
		 */
		$xlvoVote = xlvoVote::find($vote_id);

		return $xlvoVote;
	}


	/**
	 * @param $obj_id
	 *
	 * @return xlvoVotingConfig
	 */
	public function getVotingConfig($obj_id) {
		/**
		 * @var xlvoVotingConfig $xlvoVotingConfig
		 */
		$xlvoVotingConfig = xlvoVotingConfig::find($obj_id);

		return $xlvoVotingConfig;
	}


	/**
	 * @param xlvoVotingConfig $xlvoVotingConfig
	 *
	 * @return xlvoVotingConfig
	 */
	public function updateVotingConfig(xlvoVotingConfig $xlvoVotingConfig) {
		/**
		 * @var xlvoVotingConfig $xlvoVotingConfig
		 */
		$xlvoVotingConfig->update();

		return $xlvoVotingConfig;
	}


	/**
	 * @return xlvoVotingConfig
	 */
	public function getVotingConfigs() {
		/**
		 * @var xlvoVotingConfig $xlvoVotingConfigs
		 */
		$xlvoVotingConfigs = xlvoVotingConfig::getCollection();

		return $xlvoVotingConfigs;
	}


	/**
	 * @param $obj_id
	 *
	 * @return xlvoPlayer
	 */
	public function getPlayer($obj_id) {
		/**
		 * @var xlvoPlayer $xlvoPlayer
		 */
		$xlvoPlayer = xlvoPlayer::where(array( 'obj_id' => $obj_id ))->first();

		return $xlvoPlayer;
	}


	/**
	 * @param xlvoPlayer $xlvoPlayer
	 *
	 * @return xlvoPlayer
	 */
	public function updatePlayer(xlvoPlayer $xlvoPlayer) {

		$xlvoPlayer->update();

		return $xlvoPlayer;
	}


	/**
	 * @param xlvoVote $vote
	 *
	 * @return ActiveRecord|null|xlvoVote
	 * @throws xlvoVotingManagerException
	 */
	public function vote(xlvoVote $vote) {
		if ($vote->getOptionId() == NULL) {
			throw new xlvoVotingManagerException('Passed object has no optionId assigned.');
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
		 * @var xlvoVote[] $existing_votes
		 */
		$existing_votes = $this->getVotesOfUserOfVoting($xlvoOption->getVotingId())->get();

		// TODO if not anonymous check access
		$hasAccess = false;
		$isAnonymousVoting = $xlvoVotingConfig->isAnonymous();
		if ($isAnonymousVoting == 0) {
			$hasAccess = $this->access->hasReadAccessForObject($obj_id, $this->user_ilias->getId());
		} elseif ($isAnonymousVoting) {
			$hasAccess = true;
		}

		if (! $xlvoPlayer->isFrozenOrUnattended() && $xlvoPlayer->getStatus() == xlvoPlayer::STAT_RUNNING && $this->isVotingAvailable($obj_id)
			&& $hasAccess
		) {

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
								if ($vote->getStatus() != xlvoVote::STAT_INACTIVE) {
									$vote = $this->updateVote($vo, $vote);
								} else {
									$vote = $this->deleteVote($vote);
								}
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

			if ($vote instanceof xlvoVote) {
				return $vote;
			} else {
				throw new xlvoVotingManagerException('Returned object is not an instance of xlvoVote.');
			}
		} else {
			throw new xlvoVotingManagerException('Could not save vote.');
		}
	}


	/**
	 * @param xlvoVotingConfig $config
	 * @param xlvoOption       $option
	 * @param xlvoVote         $vote
	 *
	 * @return ActiveRecord
	 */
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

		/**
		 * @var xlvoVote $created_vote
		 */
		$created_vote = $this->getVotesOfUserOfOption($option->getVotingId(), $option->getId())->last();

		return $created_vote;
	}


	/**
	 * @param xlvoVote $existing_vote
	 * @param xlvoVote $new_vote
	 *
	 * @return ActiveRecord
	 */
	protected function updateVote(xlvoVote $existing_vote, xlvoVote $new_vote) {
		$existing_vote->setFreeInput($new_vote->getFreeInput());
		$existing_vote->setOptionId($new_vote->getOptionId());
		$existing_vote->update();
		$updated_vote = xlvoVote::find($existing_vote->getId());

		return $updated_vote;
	}


	/**
	 * @param xlvoVote $vote
	 *
	 * @return xlvoVote
	 */
	protected function deleteVote(xlvoVote $vote) {
		$vote->delete();
		$deleted_vote = new xlvoVote();
		$deleted_vote->setStatus(xlvoVote::STAT_INACTIVE);
		$deleted_vote->setVotingId($vote->getVotingId());
		$deleted_vote->setOptionId($vote->getOptionId());

		return $deleted_vote;
	}


	/**
	 * @param $option_id
	 *
	 * @return bool
	 */
	public function deleteVotesOfOption($option_id) {

		/**
		 * @var xlvoVote $votes
		 */
		$votes = $this->getVotesOfOption($option_id);

		foreach ($votes->get() as $vote) {
			$vote->delete();
		}

		return true;
	}


	/**
	 * @param $voting_id
	 *
	 * @return bool
	 */
	public function deleteVotesOfVoting($voting_id) {
		/**
		 * @var xlvoVote $votes
		 */
		$votes = $this->getVotesOfVoting($voting_id);
		foreach ($votes->get() as $vote) {
			$vote->delete();
		}

		return true;
	}


	/**
	 * @param $obj_id
	 *
	 * @return bool
	 */
	public function deleteVotesOfObject($obj_id) {
		/**
		 * @var xlvoVoting $votings
		 */
		$votings = xlvoVoting::where(array( 'obj_id' => $obj_id ));
		foreach ($votings as $voting) {
			$this->deleteVotesOfVoting($voting);
		}

		return true;
	}


	/**
	 * @param $obj_id
	 *
	 * @return bool
	 */
	public function isVotingAvailable($obj_id) {
		/**
		 * @var $xlvoVotingConfig xlvoVotingConfig
		 */
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
		/**
		 * @var xlvoPlayer $xlvoPlayer
		 */
		$xlvoPlayer = $this->getPlayer($xlvoVoting->getObjId());
		if ($xlvoPlayer == NULL) {
			$xlvoPlayer = new xlvoPlayer();
			$xlvoPlayer->setObjId($xlvoVoting->getObjId());
			$xlvoPlayer->setActiveVoting($voting_id);
			$xlvoPlayer->setStatus(xlvoPlayer::STAT_START_VOTING);
			$xlvoPlayer->setFrozen(true);
			$xlvoPlayer->setTimestampRefresh(time());
			$xlvoPlayer->create();
		} else {
			$xlvoPlayer->setActiveVoting($voting_id);
			$xlvoPlayer->setStatus(xlvoPlayer::STAT_RUNNING);
			$xlvoPlayer->setTimestampRefresh(time());
			$xlvoPlayer->update();
		}
	}


	/**
	 * @param $obj_id
	 *
	 * @return int
	 * @throws xlvoVotingManagerException
	 */
	public function getActiveVoting($obj_id) {
		/**
		 * @var xlvoPlayer $xlvoPlayer
		 */
		$xlvoPlayer = $this->getPlayer($obj_id);
		if ($xlvoPlayer instanceof xlvoPlayer) {
			return $xlvoPlayer->getActiveVoting();
		} else {
			throw new xlvoVotingManagerException('Returned object is not an instance of xlvoPlayer.');
		}
	}


	/**
	 * @param $obj_id
	 *
	 * @return ActiveRecord
	 * @throws xlvoVotingManagerException
	 */
	public function getActiveVotingObject($obj_id) {
		$voting_id = $this->getActiveVoting($obj_id);

		$xlvoVoting = xlvoVoting::find($voting_id);

		if($xlvoVoting instanceof xlvoVoting) {
			return $xlvoVoting;
		} else {
			throw new xlvoVotingManagerException('Returned object is not an instance of xlvoVoting.');
		}

	}


	/**
	 * @param $obj_id
	 *
	 * @throws Exception
	 */
	public function freezeVoting($obj_id) {
		try {
			/**
			 * @var xlvoPlayer $xlvoPlayer
			 */
			$xlvoPlayer = $this->getPlayer($obj_id);
			$xlvoPlayer->setFrozen(true);
			$this->updatePlayer($xlvoPlayer);
		} catch (Exception $e) {
			throw $e;
		}
	}


	/**
	 * @param $obj_id
	 *
	 * @throws Exception
	 */
	public function unfreezeVoting($obj_id) {
		try {
			/**
			 * @var xlvoPlayer $xlvoPlayer
			 */
			$xlvoPlayer = $this->getPlayer($obj_id);
			$xlvoPlayer->setFrozen(false);
			$this->updatePlayer($xlvoPlayer);
		} catch (Exception $e) {
			throw $e;
		}
	}


	/**
	 * @param $obj_id
	 *
	 * @throws Exception
	 */
	public function terminateVoting($obj_id) {
		try {
			/**
			 * @var xlvoPlayer $xlvoPlayer
			 */
			$xlvoPlayer = $this->getPlayer($obj_id);
			$this->freezeVoting($obj_id);
			$xlvoPlayer->setStatus(xlvoPlayer::STAT_STOPPED);
			$this->updatePlayer($xlvoPlayer);
		} catch (Exception $e) {
			throw $e;
		}
	}
}