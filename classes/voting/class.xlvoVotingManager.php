<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVotingInterface.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVote.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/class.xlvoVoting.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingType.php');
require_once('./Services/Object/classes/class.ilObject2.php');

/**
 *
 */
class xlvoVotingManager implements xlvoVotingInterface {

	const NEW_VOTE = 0;
	/**
	 * @var int
	 */
	protected $obj_id;
	/**
	 * @var ilUser
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


	public function getVotings($obj_id = NULL) {
		$obj_id = $obj_id ? $obj_id : $this->obj_id;
		$xlvoVotings = xlvoVoting::where(array( 'obj_id' => $obj_id ));

		return $xlvoVotings;
	}


	/**
	 * @param $id
	 *
	 * @return ActiveRecord|null
	 */
	public function getVoting($id) {
		$xlvoVoting = xlvoVoting::find($id);
		if ($xlvoVoting instanceof xlvoVoting) {
			$xlvoOptions = $this->getOptionsForVoting($xlvoVoting->getId());
			$xlvoVoting->setVotingOptions($xlvoOptions);

			return $xlvoVoting;
		} else {
			return NULL;
		}
	}


	/**
	 * @param           $voting_id
	 * @param bool|true $only_active_options
	 *
	 * @return $this|ActiveRecordList
	 * @throws Exception
	 */
	public function getOptionsForVoting($voting_id, $only_active_options = true) {
		$xlvoOptions = xlvoOption::where(array( 'voting_id' => $voting_id ));
		if ($only_active_options) {
			$xlvoOptions = $xlvoOptions->where(array( 'status' => xlvoOption::STAT_ACTIVE ));
		}

		return $xlvoOptions;
	}


	public function getOption($option_id) {
		$xlvoOption = xlvoOption::find($option_id);

		return $xlvoOption;
	}


	public function getVotes($voting_id = NULL, $option_id = NULL, $active_user = false) {
		$xlvoVotes = new xlvoVote();
		if ($option_id != NULL) {
			$xlvoVotes = $xlvoVotes->where(array( 'voting_id' => $voting_id ));
		}
		if ($option_id != NULL) {
			$xlvoVotes = $xlvoVotes->where(array( 'option_id' => $option_id ));
		}
		if ($active_user) {
			// TODO anonymous
			$xlvoVotes = $xlvoVotes->where(array( 'user_id' => $this->user_ilias->getId() ));
		}

		return $xlvoVotes;
	}


	/**
	 * @param xlvoVote $vote
	 *
	 * @return bool
	 */
	public function vote(xlvoVote $vote) {
		if ($vote->getOptionId() == NULL) {
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
		 * @var xlvoVotingConfig $xlvoVotingConfig
		 */
		$xlvoVotingConfig = $this->getVotingConfig($xlvoVoting->getObjId());

		/**
		 * @var xlvoVote[] $exisiting_votes
		 */
		$existing_votes = $this->getVotes($xlvoOption->getVotingId(), NULL, true)->get();

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
	}


	private function createVote(xlvoVotingConfig $config, xlvoOption $option, xlvoVote $vote) {
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
				/**
				 * @var ilSessionControl $ilSessionControl
				 */
				global $ilSessionControl;
				// TODO sessionId
				break;
		}

		$vote->create();
		$created_vote = $this->getVotes($option->getVotingId(), $option->getId(), true)->last();

		return $created_vote;
	}


	private function updateVote(xlvoVote $existing_vote, xlvoVote $new_vote) {
		$existing_vote->setFreeInput($new_vote->getFreeInput());
		$existing_vote->setOptionId($new_vote->getOptionId());
		$existing_vote->update();
		$updated_vote = xlvoVote::find($existing_vote->getId());

		return $updated_vote;
	}


	private function deleteVote(xlvoVote $vote) {
		$vote->delete();
		$deleted_vote = new xlvoVote();
		$deleted_vote->setStatus(xlvoVote::STAT_INACTIVE);
		$deleted_vote->setVotingId($vote->getVotingId());
		$deleted_vote->setOptionId($vote->getOptionId());

		return $deleted_vote;
	}

	public function deleteVotesForOption($option_id) {
		$votes = $this->getVotes(NULL, $option_id);

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

	public function deleteVotesAll($obj_id) {
		$votings = xlvoVoting::where(array('obj_id' => $obj_id));
		foreach($votings as $voting) {
			$this->deleteVotesForVoting($voting);
		}
		return true;
	}


	public function getVotingConfig($obj_id = NULL) {
		$obj_id = $obj_id ? $obj_id : $this->obj_id;
		$xlvoVotingConfig = xlvoVotingConfig::find($obj_id);

		return $xlvoVotingConfig;
	}
}