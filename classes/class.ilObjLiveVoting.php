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
require_once('./Services/Repository/classes/class.ilObjectPlugin.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/class.xlvoVotingConfig.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Player/class.xlvoPlayer.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voting/class.xlvoVoting.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Vote/class.xlvoVote.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Option/class.xlvoOption.php');

/**
 * Class ilObjLiveVoting
 *
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 *
 * @version $Id$
 */
class ilObjLiveVoting extends ilObjectPlugin {

	const PIN_LENGTH = 4;
	/**
	 * @var ilDB
	 */
	protected $db;


	/**
	 * @param int $a_ref_id
	 * @param bool|false $by_oid
	 */
	function __construct($a_ref_id = 0, $by_oid = false) {
		parent::__construct($a_ref_id);
		if ($a_ref_id != 0) {
			$this->id = $a_ref_id;
			$this->doRead();
		}
		global $ilDB;
		/**
		 * @var $ilDB   ilDB
		 * @var $by_oid int
		 */
		$this->db = $ilDB;
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
		$pin = $this->createPin();
		$config = new xlvoVotingConfig();
		$config->setObjId($this->getId());
		$config->setPin($pin);
		$config->setTerminable(false);
		$config->setAnonymous(false);
		$config->setObjOnline(false);
		$config->save();
	}


	/**
	 * @return string
	 */
	function createPin() {

		$length = self::PIN_LENGTH;

		$array = array();

		// numbers
		for ($i = 48; $i < 58; $i ++) {
			$array[] = chr($i);
		}

		// lower case
		for ($i = 97; $i <= 122; $i ++) {
			$array[] = chr($i);
		}

		// upper case
		for ($i = 65; $i <= 90; $i ++) {
			$array[] = chr($i);
		}

		$pin = '';
		$pin_found = false;

		while (!$pin_found) {
			for ($i = 1; $i <= $length; $i ++) {
				$rnd = mt_rand(0, count($array) - 1);
				$pin .= $array[$rnd];
			}
			if (xlvoVotingConfig::where(array( 'pin' => $pin ))->count() <= 0) {
				$pin_found = true;
			}
		}

		return $pin;
	}


	/**
	 * Read data from db
	 */
	function doRead() {
	}


	/**
	 * Update data
	 */
	function doUpdate() {
	}


	public function doDelete() {

		/**
		 * @var $players xlvoPlayer[]
		 */
		$players = xlvoPlayer::where(array( 'obj_id' => $this->getId() ))->get();
		foreach ($players as $player) {
			$player->delete();
		}

		/**
		 * @var $votings xlvoVoting[]
		 */
		$votings = xlvoVoting::where(array( 'obj_id' => $this->getId() ))->get();
		foreach ($votings as $voting) {
			$voting_id = $voting->getId();

			/**
			 * @var $votes xlvoVote[]
			 */
			$votes = xlvoVote::where(array( 'voting_id' => $voting_id ))->get();
			foreach ($votes as $vote) {
				$vote->delete();
			}

			/**
			 * @var $options xlvoOption[]
			 */
			$options = xlvoOption::where(array( 'voting_id' => $voting_id ))->get();
			foreach ($options as $option) {
				$option->delete();
			}

			$voting->delete();
		}

		/**
		 * @var $config xlvoVotingConfig
		 */
		$config = xlvoVotingConfig::find($this->getId());
		if ($config instanceof xlvoVotingConfig) {
			$config->delete();
		}
	}


	/**
	 * @param                 $a_target_id
	 * @param                 $a_copy_id
	 * @param ilObjLiveVoting $new_obj
	 */
	public function doCloneObject(ilObjLiveVoting $new_obj, $a_target_id, $a_copy_id) {

		/**
		 * @var $config xlvoVotingConfig
		 */
		$config = xlvoVotingConfig::find($this->getId());
		if ($config instanceof xlvoVotingConfig) {
			/**
			 * @var $config_clone xlvoVotingConfig
			 */
			$config_clone = $config->copy();
			$config_clone->setObjId($new_obj->getId());
			// set unique pin for cloned object
			$pin = $this->createPin();
			$config_clone->setPin($pin);
			$config_clone->update();
		}

		/**
		 * @var $player       xlvoPlayer
		 * @var $player_clone xlvoPlayer
		 */
		$player = xlvoPlayer::where(array( 'obj_id' => $this->getId() ))->first();
		if ($player instanceof xlvoPlayer) {
			$player_clone = $player->copy();
			// reset active Voting in player
			$player_clone->setActiveVoting(0);
			$player_clone->setObjId($new_obj->getId());
			$player_clone->create();
		}

		/**
		 * @var $votings xlvoVoting[]
		 */
		$votings = xlvoVoting::where(array( 'obj_id' => $this->getId() ))->get();
		foreach ($votings as $voting) {

			/**
			 * @var $voting_clone xlvoVoting
			 */
			$voting_clone = $voting->copy();
			$voting_clone->setObjId($new_obj->getId());
			$voting_clone->create();

			$voting_id = $voting->getId();
			$voting_id_clone = xlvoVoting::where(array( 'obj_id' => $new_obj->getId() ))->last()->getId();

			/**
			 * @var $options xlvoOption[]
			 */
			$options = xlvoOption::where(array( 'voting_id' => $voting_id ))->get();
			foreach ($options as $option) {
				/**
				 * @var $option_clone xlvoOption
				 */
				$option_clone = $option->copy();
				$option_clone->setVotingId($voting_id_clone);
				$option_clone->create();

				$option_id_clone = xlvoOption::where(array( 'voting_id' => $voting_id_clone ))->last()->getId();

				/**
				 * @var $votes xlvoVote[]
				 */
				$votes = xlvoVote::where(array( 'voting_id' => $voting_id ))->get();
				foreach ($votes as $vote) {
					/**
					 * @var $vote_clone xlvoVote
					 */
					$vote_clone = $vote->copy();
					$vote_clone->setVotingId($voting_id_clone);
					$vote_clone->setOptionId($option_id_clone);
					$vote_clone->create();
				}
			}
		}
	}
}
