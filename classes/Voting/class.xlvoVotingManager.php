<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVotingInterface.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Exceptions/class.xlvoVotingManagerException.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Vote/class.xlvoVote.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVoting.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/class.xlvoPlayer.php');
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
	 * @return ActiveRecordList
	 */
	public function getActiveVotings($obj_id) {
		/**
		 * @var ActiveRecordList $xlvoVotings
		 */
		$xlvoVotings = xlvoVoting::where(array(
			'obj_id' => $obj_id,
			'voting_status' => xlvoVoting::STAT_ACTIVE
		))->orderBy('position', 'ASC');

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
			throw new xlvoVotingManagerException('Returned object is not an instance of xlvoVoting.');
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
			throw new xlvoVotingManagerException('Returned object is not an instance of xlvoOption.');
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
	 * @throws Exception
	 * @throws xlvoVotingManagerException
	 */
	public function getVotesOfUserOfOption($voting_id, $option_id) {
		/**
		 * @var xlvoVote $xlvoVotes
		 */
		$xlvoVotes = $this->getVotesOfOption($option_id);

		try {
			/**
			 * @var $xlvoVoting xlvoVoting
			 */
			$xlvoVoting = $this->getVoting($voting_id);
			/**
			 * @var xlvoVotingConfig $xlvoConfig
			 */
			$xlvoConfig = $this->getVotingConfig($xlvoVoting->getObjId());
		} catch (xlvoVotingManagerException $e) {
			throw $e;
		}

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
	 * @throws Exception
	 * @throws xlvoVotingManagerException
	 */
	public function getVotesOfUserOfVoting($voting_id) {
		/**
		 * @var xlvoVote $xlvoVotes
		 */
		$xlvoVotes = $this->getVotesOfVoting($voting_id);

		try {
			/**
			 * @var $xlvoVoting xlvoVoting
			 */
			$xlvoVoting = $this->getVoting($voting_id);
			/**
			 * @var xlvoVotingConfig $xlvoConfig
			 */
			$xlvoConfig = $this->getVotingConfig($xlvoVoting->getObjId());
		} catch (xlvoVotingManagerException $e) {
			throw $e;
		}

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
	 * @throws xlvoVotingManagerException
	 */
	public function getVote($vote_id) {
		/**
		 * @var xlvoVote $xlvoVote
		 */
		$xlvoVote = xlvoVote::find($vote_id);

		if ($xlvoVote instanceof xlvoVote) {
			return $xlvoVote;
		} else {
			throw new xlvoVotingManagerException('Returned object is not an instance of xlvoVote.');
		}
	}


	/**
	 * @param $obj_id
	 *
	 * @return xlvoVotingConfig
	 * @throws xlvoVotingManagerException
	 */
	public function getVotingConfig($obj_id) {
		/**
		 * @var xlvoVotingConfig $xlvoVotingConfig
		 */
		$xlvoVotingConfig = xlvoVotingConfig::find($obj_id);

		if ($xlvoVotingConfig instanceof xlvoVotingConfig) {
			return $xlvoVotingConfig;
		} else {
			throw new xlvoVotingManagerException('Returned object is not an instance of xlvoVotingConfig.');
		}
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
	 * @throws xlvoVotingManagerException
	 */
	public function getPlayer($obj_id) {
		/**
		 * @var xlvoPlayer $xlvoPlayer
		 */
		$xlvoPlayer = xlvoPlayer::where(array( 'obj_id' => $obj_id ))->first();

		if ($xlvoPlayer instanceof xlvoPlayer) {
			return $xlvoPlayer;
		} else {
			throw new xlvoVotingManagerException('Returned object is not an instance of xlvoPlayer.');
		}
	}


	/**
	 * @param     $obj_id
	 * @param int $voting_id
	 */
	public function createPlayer($obj_id, $voting_id = 0) {
		$xlvoPlayer = new xlvoPlayer();
		$xlvoPlayer->setObjId($obj_id);
		$xlvoPlayer->setActiveVoting($voting_id);
		$xlvoPlayer->setStatus(xlvoPlayer::STAT_START_VOTING);
		$xlvoPlayer->setFrozen(true);
		$xlvoPlayer->setTimestampRefresh(time());
		$xlvoPlayer->create();
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

		try {
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
		} catch (xlvoVotingManagerException $e) {
			throw $e;
		}

		$hasAccess = false;
		$isAnonymousVoting = $xlvoVotingConfig->isAnonymous();
		if ($isAnonymousVoting == 0) {
			$hasAccess = $this->access->hasReadAccessForObject($obj_id, $this->user_ilias->getId());
		} elseif ($isAnonymousVoting) {
			$hasAccess = true;
		}

		if (!$xlvoPlayer->isFrozenOrUnattended() && $xlvoPlayer->getStatus() == xlvoPlayer::STAT_RUNNING && $this->isVotingAvailable($obj_id)
			&& $hasAccess
		) {

			/*
			 * SINGLE VOTE
			 */
			if ($xlvoVoting->getVotingType() == xlvoVotingType::TYPE_SINGLE_VOTE) {
				if ($xlvoVoting->isMultiSelection()) {
					if ($vote->getId() != self::NEW_VOTE) {
						foreach ($existing_votes as $vo) {
							if ($vote->getId() == $vo->getId()) {
								$this->deleteVote($vo);
							}
						}
					} else {
						$this->createVote($xlvoVotingConfig, $xlvoOption, $vote);
					}
				} else {
					if (count($existing_votes) > 0) {
						foreach ($existing_votes as $vo) {
							if ($xlvoOption->getId() == $vo->getOptionId()) {
								$this->deleteVote($vo);
							} else {
								$this->updateVote($vo, $vote);
							}
						}
					} else {
						$this->createVote($xlvoVotingConfig, $xlvoOption, $vote);
					}
				}
			}

			/*
			 * FREE INPUT
			 */
			if ($xlvoVoting->getVotingType() == xlvoVotingType::TYPE_FREE_INPUT) {

				if ($xlvoVoting->isMultiFreeInput()) {
					if ($vote->getId() != self::NEW_VOTE) {
						foreach ($existing_votes as $vo) {
							if ($vote->getId() == $vo->getId()) {
								if ($vote->getStatus() != xlvoVote::STAT_INACTIVE) {
									$this->updateVote($vo, $vote);
								} else {
									$this->deleteVote($vote);
									$vote = new xlvoVote();
									$vote->setStatus(xlvoVote::STAT_INACTIVE);
									$vote->setVotingId(0);
								}
							}
						}
					} else {
						$this->createVote($xlvoVotingConfig, $xlvoOption, $vote);
					}
				} else {
					if (count($existing_votes) > 0) {
						foreach ($existing_votes as $vo) {
							if ($xlvoOption->getId() == $vo->getOptionId()) {
								if ($vote->getStatus() == xlvoVote::STAT_INACTIVE) {
									$this->deleteVote($vote);
									$vote = new xlvoVote();
									$vote->setStatus(xlvoVote::STAT_INACTIVE);
									$vote->setVotingId(0);
								} else {
									$this->updateVote($vo, $vote);
								}
							}
						}
					} else {
						$this->createVote($xlvoVotingConfig, $xlvoOption, $vote);
					}
				}
			}
		} else {
			throw new xlvoVotingManagerException('Could not save vote.');
		}
	}


	/**
	 * @param xlvoVotingConfig $config
	 * @param xlvoOption $option
	 * @param xlvoVote $vote
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
	}


	/**
	 * @param xlvoVote $existing_vote
	 * @param xlvoVote $new_vote
	 */
	protected function updateVote(xlvoVote $existing_vote, xlvoVote $new_vote) {
		$existing_vote->setFreeInput($new_vote->getFreeInput());
		$existing_vote->setOptionId($new_vote->getOptionId());
		$existing_vote->update();
	}


	/**
	 * @param xlvoVote $vote
	 */
	protected function deleteVote(xlvoVote $vote) {
		$vote->delete();
	}


	/**
	 * @param $option_id
	 */
	public function deleteVotesOfOption($option_id) {

		/**
		 * @var xlvoVote $votes
		 */
		$votes = $this->getVotesOfOption($option_id);

		foreach ($votes->get() as $vote) {
			$vote->delete();
		}
	}


	/**
	 * @param $voting_id
	 */
	public function deleteVotesOfVoting($voting_id) {
		/**
		 * @var xlvoVote $votes
		 */
		$votes = $this->getVotesOfVoting($voting_id);
		foreach ($votes->get() as $vote) {
			$vote->delete();
		}
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
		$xlvoVotingConfig = $this->getVotingConfig($obj_id);
		$terminable = $xlvoVotingConfig->isTerminable();

		if (!$terminable) {
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
	 *
	 * @throws Exception
	 */
	public function setActiveVoting($voting_id) {
		try {
			/**
			 * @var xlvoVoting $xlvoVoting
			 */
			$xlvoVoting = $this->getVoting($voting_id);
		} catch (xlvoVotingManagerException $e) {
			throw $e;
		}

		try {
			/**
			 * @var xlvoPlayer $xlvoPlayer
			 */
			$xlvoPlayer = $this->getPlayer($xlvoVoting->getObjId());
			$xlvoPlayer->setActiveVoting($voting_id);
			$xlvoPlayer->setStatus(xlvoPlayer::STAT_RUNNING);
			$xlvoPlayer->setTimestampRefresh(time());
			$this->updatePlayer($xlvoPlayer);
		} catch (xlvoVotingManagerException $e) {
			$this->createPlayer($xlvoVoting->getObjId(), $voting_id);
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
			$voting_id = $xlvoPlayer->getActiveVoting();
			if ($voting_id >= 0) {
				return $voting_id;
			} else {
				throw new xlvoVotingManagerException('Returned value is not a number.');
			}
		} else {
			throw new xlvoVotingManagerException('Returned object is not an instance of xlvoPlayer.');
		}
	}


	/**
	 * @param $obj_id
	 *
	 * @return xlvoVoting
	 * @throws xlvoVotingManagerException
	 */
	public function getActiveVotingObject($obj_id) {
		$voting_id = $this->getActiveVoting($obj_id);

		try {
			$xlvoVoting = $this->getVoting($voting_id);
		} catch (xlvoVotingManagerException $e) {
			throw $e;
		}

		if ($xlvoVoting instanceof xlvoVoting) {
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
		/**
		 * @var xlvoPlayer $xlvoPlayer
		 */
		$xlvoPlayer = $this->getPlayer($obj_id);
		$xlvoPlayer->setFrozen(true);
		$this->updatePlayer($xlvoPlayer);
	}


	/**
	 * @param $obj_id
	 *
	 * @throws Exception
	 */
	public function unfreezeVoting($obj_id) {
		/**
		 * @var xlvoPlayer $xlvoPlayer
		 */
		$xlvoPlayer = $this->getPlayer($obj_id);
		$xlvoPlayer->setFrozen(false);
		$this->updatePlayer($xlvoPlayer);
	}


	/**
	 * @param $obj_id
	 *
	 * @throws Exception
	 */
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