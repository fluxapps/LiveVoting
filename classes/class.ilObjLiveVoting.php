<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/
include_once("./Services/Repository/classes/class.ilObjectPlugin.php");
include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingOption.php");
include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.ilLiveVotingVote.php");
include_once('class.ilLiveVotingConfigGUI.php');

/**
 * Application class for example repository object.
 *
 * @author Oskar Truffer <ot@studer-raimann.ch>
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 * $Id$
 */
class ilObjLiveVoting extends ilObjectPlugin {

	const IL_LIVEVOTE_SINGLE_CHOICE = 0;
	const IL_LIVEVOTE_MULTIPLE_CHOICE = 1;
	const IL_LIVEVOTE_PINSIZE = 4;
	const EXPIRED = "Expired";
	/**
	 * determines the number of DAYS a pin is valid before it eventually will get removed (when the next item is created to be specific).
	 */
	const PIN_VALIDITY = 180;
	/**
	 * @var bool
	 */
	protected $online;
	/**
	 * @var array
	 */
	protected $config;
	/**
	 * @var bool
	 */
	protected $anonym;
	/**
	 * @var bool
	 */
	protected $colorful;
	/**
	 * @var string
	 */
	protected $pin;
	/**
	 * @var int
	 */
	protected $options_type;
	/**
	 * @var ilLiveVotingOption[]
	 */
	protected $options;
	/**
	 * @var string
	 */
	protected $question;
	/**
	 * @var int
	 */
	protected $start;
	/**
	 * @var int
	 */
	protected $end;
	/**
	 * @var bool
	 */
	protected $terminated;
	/**
	 * @var bool
	 */
	protected $freezed;


	/**
	 * Constructor
	 *
	 * @access    public
	 */
	function __construct($a_ref_id = 0, $by_oid = false) {
		parent::__construct($a_ref_id, $by_oid);
		if ($a_ref_id != 0) {
			$this->id = $a_ref_id;
			$this->doRead();
		}
	}


	/**
	 * Get type.
	 */
	final function initType() {
		$this->setType("xlvo");
	}


	/**
	 * Create object
	 */
	function doCreate() {
		global $ilDB;
		$pin = $this->createPin();
		$ilDB->manipulate("INSERT INTO rep_robj_xlvo_data "
			. "(id, is_online, options_type, is_anonym, is_colorful, pin, question, is_terminated, is_freezed, start_time, end_time) VALUES ("
			. $ilDB->quote($this->getId(), "integer") . "," . // id
			$ilDB->quote(1, "integer") . "," . // is_online
			$ilDB->quote(0, "integer") . "," . // options_type
			$ilDB->quote(1, "integer") . "," . // is_anonym
			$ilDB->quote(0, "integer") . "," . // is_colorful
			$ilDB->quote($pin, "text") . "," . // pin
			$ilDB->quote("", "text") . "," . // question
			$ilDB->quote(0, "integer") . "," . // is_terminated
			$ilDB->quote(0, "integer") . "," . // is_freezed
			$ilDB->quote(0, "integer") . "," . // start_time
			$ilDB->quote(0, "integer") . // end_time
			")");
	}


	/**
	 * Read data from db
	 */
	function doRead() {
		global $ilDB;
		$set = $ilDB->query("SELECT * FROM rep_robj_xlvo_data " . " WHERE id = " . $ilDB->quote($this->getId(), "integer"));
		//echo "<pre>".print_r($ilDB->fetchAssoc($set), 1)."</pre>";
		while ($rec = $ilDB->fetchAssoc($set)) {
			$this->setOnline($rec["is_online"]); // is_online
			$this->setOptionsType($rec['options_type']); // options_type
			$this->setAnonym($rec["is_anonym"]); // is_anonym
			$this->setColorful($rec["is_colorful"]); // is_colorful
			$this->setPin($rec["pin"]); // pin
			$this->setQuestion($rec["question"]); // question
			$this->setTerminated($rec["is_terminated"]); // is_terminated
			$this->setFreezed($rec["is_freezed"]); // is_terminated
			$this->setStart($rec["start_time"]); // start_time
			$this->setEnd($rec["end_time"]); // end_time
		}
	}


	/**
	 * Update data
	 */
	function doUpdate() {
		global $ilDB;
		$ilDB->manipulate($up = "UPDATE rep_robj_xlvo_data SET " . " is_online = " . $ilDB->quote($this->getOnline(), "integer") . "," . // is_online
			" is_anonym = " . $ilDB->quote($this->getAnonym(), "text") . "," . // is_anonym
			" is_colorful= " . $ilDB->quote($this->getColorful(), "text") . "," . // is_colorful
			" options_type = " . $ilDB->quote($this->getOptionsType(), "text") . "," . // options_type
			" pin = " . $ilDB->quote($this->getPin(), "text") . "," . // pin
			" question = " . $ilDB->quote($this->getQuestion(), "text") . "," . // question
			" is_terminated = " . $ilDB->quote($this->getTerminated(), "integer") . "," . // is_terminated
			" is_freezed = " . $ilDB->quote($this->getFreezed(), "integer") . "," . // is_freezed
			" start_time = " . $ilDB->quote($this->getStart(), "integer") . "," . // start_time
			" end_time = " . $ilDB->quote($this->getEnd(), "integer") . // end_time
			" WHERE id = " . $ilDB->quote($this->getId(), "integer"));
	}


	public function doDelete() {
		global $ilDB;
		$this->emptyCache();
		$this->deleteOptions();
		$ilDB->manipulate("DELETE FROM rep_robj_xlvo_data WHERE " . " id = " . $ilDB->quote($this->getId(), "integer"));
	}


	/**
	 * @param                 $a_target_id
	 * @param                 $a_copy_id
	 * @param ilObjLiveVoting $new_obj
	 */
	public function doCloneObject(ilObjLiveVoting $new_obj, $a_target_id, $a_copy_id) {
		$new_obj->setOnline($this->getOnline());
		$new_obj->setAnonym($this->getAnonym());
		$new_obj->setColorful($this->getColorful());
		$new_obj->setOptionsType($this->getOptionsType());
		// don't use the object's pin, instead create a new one, because it should be unique for each LiveVoting.
		$new_obj->setPin($new_obj->createPin());
		$new_obj->setQuestion($this->getQuestion());
		$new_obj->setTerminated($this->getTerminated());
		$new_obj->setFreezed($this->getFreezed());
		$new_obj->setStart($this->getStart());
		$new_obj->setEnd($this->getEnd());
		$new_obj->update();

		foreach ($this->getOptions() as $key => $option) {
			$new_obj->addOption($option->getTitle());
		}
	}


	/**
	 * @return string
	 */
	private function createPin() {
		global $ilDB;
		$this->freeSomePins();
		$pins = array();
		$query = "SELECT pin FROM rep_robj_xlvo_data";
		$set = $ilDB->query($query);
		while ($res = $ilDB->fetchAssoc($set)) {
			$pins[] = $res['pin'];
		}
		do {
			$pin = "";
			for ($i = 0; $i < self::IL_LIVEVOTE_PINSIZE; $i ++) {
				$pin .= rand(0, 9);
			}
		} while (in_array($pin, $pins));

		return $pin;
	}


	/**
	 * @description As the number of pin's is limited they have to be removed after a
	 * certain time span. This method gets evoked every time a new Live Voting is created,
	 * as this is the time when free pin's have to be available
	 */
	private function freeSomePins() {
		global $ilDB;
		$objs = array();
		$query = "SELECT id FROM rep_robj_xlvo_data WHERE pin NOT LIKE '" . self::EXPIRED . "'";
		$set = $ilDB->query($query);
		//if we still have enough pins it's ok.
		if (!$this->toManyPins($set->numRows())) {
			return;
		}
		//save all livevotings in a list in order to only have to load them once.
		while ($res = $ilDB->fetchAssoc($set)) {
			$objs[$res['id']] = new ilObjLiveVoting($res['id']);
		}
		//we start at the expire time.
		$expire_time = self::PIN_VALIDITY;
		//while we have too many pins we delete some. deletion is done in checkPinValidity
		while (toManyPins(count($objs))) {
			$okObjs = array();
			foreach ($objs as $obj) {
				if ($this->checkPinValidity($obj, $expire_time)) {
					$okObjs[] = $obj;
				}
			}
			$obj = $okObjs;
			$expire_time -= 5;
		}
	}


	/**
	 *
	 * @param $pins how many pins are there?
	 *
	 * @return bool true iff more than half of the available pins are taken.
	 */
	private function toManyPins($pins) {
		return $pins / pow(10, self::IL_LIVEVOTE_PINSIZE) > 0.5;
	}


	/**
	 * @param $live_voting
	 * @param $days         int How many days does it take for a pin to expire
	 *
	 * @internal param \ilLiveVoting $ilLiveVoting the live voting object.
	 * @return bool true iff the pin is not yet expired
	 */
	private function checkPinValidity($live_voting, $days) {
		$create_date_string = $live_voting->getCreateDate();
		$create_date = new ilDateTime($create_date_string);
		$expire_date = $create_date->increment(ilDateTime::DAY, $days);
		$now = new ilDateTime(time(), IL_CAL_UNIX);
		if ($now > $expire_date) {
			$live_voting->setPin(self::EXPIRED);
			$live_voting->doUpdate();

			return false;
		} else {
			return true;
		}
	}


	/**
	 * @param $option_id int The option the User wants to vote for.
	 * @param $usr_id    int The user that want's to vote, if the voting is anonym the usr_id will not be saved.
	 * @param $session   string The current session of the user voting.
	 *
	 * @return bool Returns true iff voting is succesful
	 */
	public function vote($option_id, $usr_id, $session) {
		if ($this->isActive()) {
			$options = $this->getOptions();
			if (!array_key_exists($option_id, $options)) {
				return false;
			}
			//if single vote is active only one vote per user can be given.
			if ($this->getOptionsType() == self::IL_LIVEVOTE_SINGLE_CHOICE) {
				foreach ($options as $option) {
					$option->deleteVotes($usr_id, $session);
				}
			}
			//vote for option.
			$options[$option_id]->vote($usr_id, $session, $this->getAnonym());

			return true;
		} else {
			return false;
		}
	}


	/**
	 * @param $option_id int The option the User wants to unvote.
	 * @param $usr_id    int The user that want's to unvote
	 * @param $session   string The current session of the user
	 *
	 * @return bool Returns true iff unvoting is succesful
	 */
	public function unvote($option_id, $usr_id, $session) {
		if (!$this->isActive()) {
			return false; // cannot change votes, because the LiveVoting isn't active
		}

		$options = $this->getOptions();
		if (!array_key_exists($option_id, $options)) {
			return false;
		}
		//unvote for option.
		$options[$option_id]->unvote($usr_id, $session);

		return true;
	}


	/**
	 * @param $usr_id  int
	 * @param $session string
	 */
	public function deleteAllVotes($usr_id = 0, $session = "") {
		foreach ($this->getOptions() as $option) {
			$option->deleteVotes($usr_id, $session);
		}
	}


	/**
	 * @return int
	 */
	public function getTotalVotes() {
		$votes = 0;
		foreach ($this->getOptions() as $option) {
			$votes += $option->countVotes();
		}

		return $votes;
	}


	/**
	 * @return int
	 */
	public function getAbsoluteVotes() {
		$total = 0;
		foreach ($this->getOptions() as $option) {
			$votes = $option->getVotes();
			if (is_array($votes) AND count($votes) != 0) {
				foreach ($votes as $vote) {
					$users[] = $vote->getUsrSession();
				}
			}
		}

		return count($users);
	}


	/**
	 * Get the number of absolute votes given.
	 */
	public function getAbsoluteVotesOld() {
		foreach ($this->getOptions() as $option) {
			foreach ($option->getVotes() as $vote) {
				$users[] = $vote->getUsrSession();
			}
		}

		return count(array_unique($users));
	}


	/**
	 * @param $array array id => title
	 */
	public function setOptionsByArray(array $array) {
		//		echo '<pre>'.print_r($array,1).'</pre>';
		//		exit;
		$options = $this->getOptions();
		foreach ($array as $id => $title) {
			if (array_key_exists($id, $options)) {
				$options[$id]->setTitle($title);
				$options[$id]->doUpdate();
			} else {
				$this->addOption($title);
			}
		}
		foreach ($options as $option) {
			if (!array_key_exists($option->getId(), $array)) {
				$this->deleteOption($option->getId());
			}
		}
	}


	/**
	 * deletes all options of this voting (incl. it's votes).
	 */
	public function deleteOptions() {
		foreach ($this->getOptions() as $option) {
			$option->doDelete();
		}
	}


	/**
	 * @param $option_id int the id of the option.
	 *
	 * @return bool returns true iff an option with the given id exists and is successfuly deleted.
	 */
	public function deleteOption($option_id) {
		$options = $this->getOptions();
		if (!array_key_exists($option_id, $options)) {
			return false;
		}
		$options[$option_id]->doDelete();

		return true;
	}


	/**
	 * @param string $title adds a new option to this voting.
	 */
	public function addOption($title) {
		if ($title === NULL OR $title === '') {
			return;
		}
		$option = new ilLiveVotingOption();
		$option->setTitle($title);
		$option->setDataId($this->getId());
		$option->doCreate();
	}


	/**
	 * @param $pin
	 *
	 * @return ilObjLiveVoting|false
	 */
	public static function _getObjectByPin($pin) {
		global $ilDB;
		$query = "SELECT id FROM rep_robj_xlvo_data WHERE pin = " . $ilDB->quote($pin, "text");
		$set = $ilDB->query($query);
		if ($set->numRows() == 0) {
			return false;
		}
		$rec = $ilDB->fetchAssoc($set);

		return ilObjectFactory::getInstanceByObjId($rec["id"]);
	}


	/**
	 * @param $pin
	 *
	 * @return bool
	 */
	public static function _isGlobalAnonymForPin($pin) {
		global $ilDB;
		$query = "SELECT is_anonym FROM rep_robj_xlvo_data WHERE pin = " . $ilDB->quote($pin, "text");
		$set = $ilDB->query($query);
		if ($set->numRows() == 0) {
			return false;
		}
		$rec = $ilDB->fetchAssoc($set);

		return (bool)$rec["is_anonym"];
	}


	public function maxVotes() {
		$max = 0;
		foreach ($this->getOptions() as $option) {
			if ($option->countVotes() > $max) {
				$max = $option->countVotes();
			}
		}

		return $max;
	}


	/**
	 * @param $pin
	 *
	 * @return string
	 */
	public static function getShortLinkByPin($pin) {
		$pl = new ilLiveVotingPlugin();
		if ($pl->getConfigObject()->getValue('allow_shortlink') AND $pl->getConfigObject()->getValue('allow_shortlink_link')
		) {
			return $pl->getConfigObject()->getValue('allow_shortlink_link') . '?pin=' . $pin;
		} else {
			return ilObjLiveVotingGUI::_getLinkByPin($pin);
		}
	}


	/**
	 * This funciton deletes all information that is cached.
	 */
	public function emptyCache() {
		$this->options = NULL;
	}


	private function loadOptions() {
		//only loads options if they're not already loaded.
		if ($this->options != NULL) {
			return;
		}
		global $ilDB;
		$query = "SELECT id FROM rep_robj_xlvo_option WHERE data_id = " . $ilDB->quote($this->getId(), "integer");
		$set = $ilDB->query($query);
		$this->options = array();
		while ($res = $ilDB->fetchAssoc($set)) {
			$this->options[$res['id']] = new ilLiveVotingOption($res['id']);
		}
	}


	/**
	 * Get's the percentage of the votes on the given option
	 *
	 * @param $option_id int which option to get the percentage for
	 *
	 * @return float the percentage like 54... not 0.54
	 */
	public function getPercentageForOption($option_id) {
		$option = $this->getOption($option_id);
		$total = $this->getTotalVotes();
		$total = ($total == 0 ? 1 : $total);

		return $option->countVotes() / $total * 100;
	}


	/**
	 * getRelativePercentageForOption
	 *
	 * gets the percentageof the votes on the given option respective to the option with the most votes
	 *
	 * @param $option_id int which option to get the percentage for
	 *
	 * @return float the percentage like 54... not 0.54
	 */
	public function getRelativePercentageForOption($option_id) {
		$option = $this->getOption($option_id);
		if ($this->maxVotes() != 0) {
			return $option->countVotes() / $this->maxVotes() * 100;
		} else {
			return 0;
		}
	}


	/**
	 * @return ilLiveVotingOption[]
	 */
	public function getOptions() {
		$this->loadOptions();

		return $this->options;
	}


	public function getOption($option_id) {
		$this->loadOptions();

		return $this->options[$option_id];
	}


	/**
	 * Checks whether users can submit votes currently
	 *
	 * @return boolean
	 */
	public function isActive() {
		if (!$this->getOnline()) // only allow votes, if the object is available itself
		{
			return false;
		}
		if ($this->getFreezed()) {
			return false;
		}
		if ($this->getTerminated()) {
			$t = time();

			return $this->getStart() < $t AND $t < $this->getEnd();
		} else {
			return true;
		}
	}


	/**
	 * @param boolean $online
	 */
	public function setOnline($online) {
		$this->online = $online;
	}


	/**
	 * @return boolean
	 */
	public function getOnline() {
		return $this->online;
	}


	/**
	 * @param string $pin
	 */
	public function setPin($pin) {
		$this->pin = $pin;
	}


	/**
	 * @return string
	 */
	public function getPin() {
		return $this->pin;
	}


	/**
	 * @param boolean $anonym
	 */
	public function setAnonym($anonym) {
		$this->anonym = $anonym;
	}


	/**
	 * @return boolean
	 */
	public function getAnonym() {
		return $this->anonym;
	}


	public function setColorful($colorful) {
		$this->colorful = $colorful;
	}


	public function getColorful() {
		return $this->colorful;
	}


	/**
	 * @param int $options_type
	 */
	public function setOptionsType($options_type) {
		$this->options_type = $options_type;
	}


	/**
	 * @return int
	 */
	public function getOptionsType() {
		return $this->options_type;
	}


	/**
	 * @param string $question
	 */
	public function setQuestion($question) {
		$this->question = $question;
	}


	/**
	 * @return string
	 */
	public function getQuestion() {
		return $this->question;
	}


	/**
	 * @param \int $start
	 */
	public function setStart($start) {
		$this->start = $start;
	}


	/**
	 * @return \int
	 */
	public function getStart() {
		return $this->start;
	}


	/**
	 * @param \int $end
	 */
	public function setEnd($end) {
		$this->end = $end;
	}


	/**
	 * @return \int
	 */
	public function getEnd() {
		return $this->end;
	}


	/**
	 * @param boolean $terminated
	 */
	public function setTerminated($terminated) {
		$this->terminated = $terminated;
	}


	/**
	 * @return boolean
	 */
	public function getTerminated() {
		return $this->terminated;
	}


	/**
	 * @param boolean $freezed
	 */
	public function setFreezed($freezed) {
		$this->freezed = $freezed;
	}


	/**
	 * @return boolean
	 */
	public function getFreezed() {
		return $this->freezed;
	}


	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	public function getConfigValue($key) {
		return ilLiveVotingConfigGUI::_getValue($key);
	}
}

?>
