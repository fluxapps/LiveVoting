<?php

class ilLiveVotingOption {

	/**
	 * @var int
	 */
	protected $id;
	/**
	 * @var int
	 */
	protected $data_id;
	/**
	 * @var string
	 */
	protected $title;
	/**
	 * @var ilLiveVotingVote[]
	 */
	protected $votes;


	function __construct($id = 0) {
		$this->id = $id;
		if ($id != 0) {
			$this->doRead();
		}
	}


	/**
	 * Create object
	 */
	function doCreate() {
		global $ilDB;

		$next_id = $ilDB->nextID('rep_robj_xlvo_option');
		$this->setId($next_id);

		$ilDB->manipulate("INSERT INTO rep_robj_xlvo_option " . "(id, data_id, title) VALUES (" . $ilDB->quote($this->getId(), "integer") . ","
			. $ilDB->quote($this->getDataId(), "integer") . "," . $ilDB->quote($this->getTitle(), "text") . ")");
	}


	/**
	 * Read data from db
	 */
	function doRead() {
		global $ilDB;

		$set = $ilDB->query("SELECT * FROM rep_robj_xlvo_option " . " WHERE id = " . $ilDB->quote($this->getId(), "integer"));
		while ($rec = $ilDB->fetchAssoc($set)) {
			$this->setDataId($rec["data_id"]);
			$this->setTitle($rec['title']);
		}
	}


	/**
	 * Update data
	 */
	function doUpdate() {
		global $ilDB;

		$ilDB->manipulate($up = "UPDATE rep_robj_xlvo_option SET " . " data_id = " . $ilDB->quote($this->getDataId(), "integer") . "," . " title = "
			. $ilDB->quote($this->getTitle(), "text") . " WHERE id = " . $ilDB->quote($this->getId(), "integer"));
	}


	/**
	 * Delete data from db
	 */
	function doDelete() {
		global $ilDB;
		$this->emptyCache();
		$this->deleteVotes();

		$ilDB->manipulate("DELETE FROM rep_robj_xlvo_option WHERE " . " id = " . $ilDB->quote($this->getId(), "integer"));
	}


	/**
	 * Vote for this option! The method prevents double votes for a user.
	 *
	 * @param $usr_id  int the voting user's id
	 * @param $session string the voting user's session
	 * @param $anonym  method saves the voting user iff anonym is false
	 */
	public function vote($usr_id, $session, $anonym) {
		$this->deleteVotes($usr_id, $session);
		$vote = new ilLiveVotingVote();
		$vote->setOptionId($this->getId());
		if (! $anonym) {
			$vote->setUsrId($usr_id);
		}
		$vote->setUsrSession($session);
		$vote->doCreate();
		$this->votes[$vote->getId()] = $vote;
	}


	public function unvote($usr_id, $session) {
		$this->deleteVotes($usr_id, $session);
	}


	/**
	 * Delete all Votes
	 *
	 * @param int    $usr_id  if specified you only delete votes from this user
	 * @param string $session if specified you only delete votes made in this session
	 */
	public function deleteVotes($usr_id = 0, $session = '') {
		$votes = $this->getVotes();
		if (is_array($votes) && count($votes) > 0) {
			foreach ($votes as $vote) {
				if ($usr_id != 0 && $vote->getUsrId() == $usr_id) {
					unset($this->votes[$vote->getId()]);
					$vote->doDelete();
				}
				if ($session != '' && $vote->getUsrSession() == $session) {
					unset($this->votes[$vote->getId()]);
					$vote->doDelete();
				}
				if ($session == '' && $usr_id == 0) {
					unset($this->votes[$vote->getId()]);
					$vote->doDelete();
				}
			}
		}
	}


	/**
	 * @param $vote_id int the id of the vote.
	 *
	 * @return bool returns true iff a vote with the specified id exists and it has successfuly been deleted.
	 */
	public function deleteVote($vote_id) {
		$votes = $this->getVotes();
		if (array_key_exists($vote_id, $votes)) {
			return false;
		}
		$votes['vote_id']->doDelete();

		return true;
	}


	/**
	 * This funciton deletes all information that is cached.
	 */
	public function emptyCache() {
		$this->votes = NULL;
	}


	private function loadVotes() {
		//only loads votes if they're not already loaded.
		if ($this->votes != NULL) {
			return;
		}
		global $ilDB;
		$query = "SELECT id FROM rep_robj_xlvo_vote WHERE option_id = " . $ilDB->quote($this->getId(), "integer");
		$set = $ilDB->query($query);
		while ($res = $ilDB->fetchAssoc($set)) {
			$this->votes[$res['id']] = new ilLiveVotingVote($res['id']);
		}
	}


	/**
	 * @return ilLiveVotingVote[]
	 */
	public function getVotes() {
		$this->loadVotes();

		return $this->votes;
	}


	public function isVoter($usr_id, $session) {
		$is_voter = false;
		$votes = $this->getVotes();

		if (is_array($votes) && count($votes) > 0) {
			foreach ($votes as $vote) {
				if (($vote->getUsrId() == $usr_id && $usr_id) || $vote->getUsrSession() == $session) {
					$is_voter = true;
				}
			}
		}

		return $is_voter;
	}


	public function countVotes() {
		$this->loadVotes();

		return count($this->votes);
	}


	/**
	 * @param int $data_id
	 */
	public function setDataId($data_id) {
		$this->data_id = $data_id;
	}


	/**
	 * @return int
	 */
	public function getDataId() {
		return $this->data_id;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}
}

?>