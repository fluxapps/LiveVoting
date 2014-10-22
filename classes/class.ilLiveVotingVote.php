<?php

class ilLiveVotingVote{

	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var int
	 */
	protected $usr_id;

	/**
	 * @var string
	 */
	protected $usr_session;

	/**
	 * @var int
	 */
	protected $option_id;

	/**
	 * @param int $id
	 */
	function __construct($id = 0)
	{
		$this->id = $id;
		if($id != 0)
			$this->doRead();
	}


	/**
	 * Create object
	 */
	function doCreate()
	{
		global $ilDB;

		$next_id = $ilDB->nextID('rep_robj_xlvo_vote');
		$this->setId($next_id);

		$ilDB->manipulate("INSERT INTO rep_robj_xlvo_vote ".
			"(id, option_id, usr_id, usr_session) VALUES (".
			$ilDB->quote($this->getId(), "integer").",".
			$ilDB->quote($this->getOptionId(), "integer").",".
			$ilDB->quote($this->getUsrId(), "integer").",".
			$ilDB->quote($this->getUsrSession(), "text").
			")");
		return true;
	}

	/**
	 * Read data from db
	 */
	function doRead()
	{
		global $ilDB;

		$set = $ilDB->query("SELECT * FROM rep_robj_xlvo_vote ".
				" WHERE id = ".$ilDB->quote($this->getId(), "integer")
		);
		while ($rec = $ilDB->fetchAssoc($set))
		{
			$this->setOptionId($rec["option_id"]);
			$this->setUsrId($rec['usr_id']);
			$this->setUsrSession($rec['usr_session']);
		}
	}

	/**
	 * Delete data from db
	 */
	function doDelete()
	{
		global $ilDB;

		$ilDB->manipulate("DELETE FROM rep_robj_xlvo_vote WHERE ".
				" id = ".$ilDB->quote($this->getId(), "integer")
		);

	}



	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $option_id
	 */
	public function setOptionId($option_id)
	{
		$this->option_id = $option_id;
	}

	/**
	 * @return int
	 */
	public function getOptionId()
	{
		return $this->option_id;
	}

	/**
	 * @param int $usr_id
	 */
	public function setUsrId($usr_id)
	{
		$this->usr_id = $usr_id;
	}

	/**
	 * @return int
	 */
	public function getUsrId()
	{
		return $this->usr_id;
	}

	/**
	 * @param string $usr_session
	 */
	public function setUsrSession($usr_session)
	{
		$this->usr_session = $usr_session;
	}

	/**
	 * @param $vote ilLiveVotingVote
	 * @return bool
	 */
	public function sameVoter($vote){
		if($this->getUsrId() != Null && $vote->getUsrId() != Null)
			return $this->getUsrId() == $vote->getUsrId();
		return $this->getUsrSession() == $vote->getUsrSession();
	}

	/**
	 * @return string
	 */
	public function getUsrSession()
	{
		return $this->usr_session;
	}


}
?>