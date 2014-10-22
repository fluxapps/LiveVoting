<?php
require_once('class.ilObjLiveVotingGUI.php');
require_once('class.ilObjLiveVoting.php');
require_once('class.ilLiveVotingSMS.php');
require_once('class.ilLiveVotingConfigGUI.php');
class ilLiveVotingStressTestSMS extends ilLiveVotingSMS {

	/**
	 * @param array $data
	 */
	public function __construct(array $data) {
		$this->setRecipient($data['ORIG']);
		if ($c = preg_match_all("/" . '(\\d+).*?((?:[a-z][a-z0-9_]*))' . "/is", $data['MSG'], $matches)) {
			$this->setPin($matches[1][0]);
			$this->setVote($matches[2][0]);
		}
		parent::__construct();
	}


	public function send() {
		return;
	}
}

?>