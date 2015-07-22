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

/**
 * ilLiveVotingStressTest
 * Stress-Test with Simultaneous Votes
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 * $Id$
 */
class ilLiveVotingStressTest {

	const TIMEOUT = 2;
	/**
	 * @var array
	 */
	protected $votes;
	/**
	 * @var int
	 */
	protected $pin;
	/**
	 * @var string
	 */
	protected $url;
	/**
	 * @var array
	 */
	protected $options;
	/**
	 * @var int
	 */
	protected $start_time;
	/**
	 * @var int
	 */
	protected $end_time;
	/**
	 * @var bool
	 */
	protected $slow;


	/**
	 * @param int  $pin
	 * @param int  $votes
	 * @param bool $slow
	 */
	public function __construct($pin = 0, $votes = 1, $slow = false) {
		require_once('class.ilObjLiveVoting.php');
		$this->object = ilObjLiveVoting::_getObjectByPin($pin);
		if (is_object($this->object)) {
			$this->setPin($this->object->getPin());
			$this->setUrl($_SERVER['HTTP_ORIGIN'] . "/Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/st.php");
			$this->setVotes($votes);
			$this->getVotes($this->object->getOptions());
			$this->setSlow($slow);
		} else {
			ilUtil::sendFailure('Wrong PIN given');

			return false;
		}
	}


	public function run() {
		$url = $this->getUrl();
		$i = 0;
		foreach ($this->object->getOptions() as $op) {
			$votes[] = chr(65 + $i);
			$i ++;
		}
		for ($x = 0; $x < $this->getVotes() / 10; $x ++) {
			for ($y = 0; $y < ($this->getSlow() ? 2 : 10); $y ++) {
				$vote = $votes[rand(0, count($votes))];
				$urls_main[$x][] = $url . "?MSG=" . $this->getPin() . $vote . "&ORIG=lvo" . $x . "x" . $y;
			}
		}
		$this->setStartTime(microtime(true));
		$dev = true;
		if ($dev == true) {
			foreach ($urls_main as $k => $urls) {
				$curl_array = array();
				$ch = curl_multi_init();
				foreach ($urls as $count => $url) {
					$curl_array[$k . "-" . $count] = curl_init($url);
					curl_setopt($curl_array[$k . "-" . $count], CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curl_array[$k . "-" . $count], CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($curl_array[$k . "-" . $count], CURLOPT_SSL_VERIFYHOST, false);
					curl_multi_add_handle($ch, $curl_array[$k . "-" . $count]);
				}
				do {
					curl_multi_exec($ch, $exec);
					if ($this->getSlow()) {
						sleep(self::TIMEOUT);
					}
				} while ($exec > 0);
			}
		}
		$this->setEndTime(microtime(true));
	}


	/**
	 * @param int $pin
	 */
	public function setPin($pin) {
		$this->pin = $pin;
	}


	/**
	 * @return int
	 */
	public function getPin() {
		return $this->pin;
	}


	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}


	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}


	/**
	 * @param array $votes
	 */
	public function setVotes($votes) {
		$this->votes = $votes;
	}


	/**
	 * @return array
	 */
	public function getVotes() {
		return $this->votes;
	}


	/**
	 * @param array $options
	 */
	public function setOptions($options) {
		$this->options = $options;
	}


	/**
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}


	/**
	 * @param int $end_time
	 */
	public function setEndTime($end_time) {
		$this->end_time = $end_time;
	}


	/**
	 * @return int
	 */
	public function getEndTime() {
		return $this->end_time;
	}


	/**
	 * @param int $start_time
	 */
	public function setStartTime($start_time) {
		$this->start_time = $start_time;
	}


	/**
	 * @return int
	 */
	public function getStartTime() {
		return $this->start_time;
	}


	/**
	 * @param boolean $slow
	 */
	public function setSlow($slow) {
		$this->slow = $slow;
	}


	/**
	 * @return boolean
	 */
	public function getSlow() {
		return $this->slow;
	}
}

?>