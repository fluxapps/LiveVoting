<?php

require_once('class.ilObjLiveVotingGUI.php');
require_once('class.ilObjLiveVoting.php');
require_once('class.ilLiveVotingSMS.php');
require_once('class.ilLiveVotingConfigGUI.php');

class ilLiveVotingSragSMS extends ilLiveVotingSMS {

	/**
	 * @var int
	 */
	protected $costs;


	/**
	 * @param array $data
	 */
	public function __construct($data) {
		if (!ilLiveVotingConfigGUI::_getValue('sragsms')) {
			exit;
		}

		$xml = simplexml_load_string($data);

		$this->setSendsmsError(true);
		$this->setSendsmsOk(true);
		$this->setCosts(ilLiveVotingConfigGUI::_getValue('sragsms_costs'));

		$this->setRecipient($xml->sender);

		$this->setRequestUrl('');
		$this->setPort(443);

		if ($c = preg_match_all("/" . '(\\d+).*?((?:[a-z][a-z0-9_]*))' . "/is", $xml->parameters->text, $matches)) {
			$this->setPin($matches[1][0]);
			$this->setVote($matches[2][0]);
		}
		$this->data = $data;
		parent::__construct();
	}


	/**
	 * Send SMS
	 */
	public function send() {
		$xml = new SimpleXMLElement("<NotificationReply></NotificationReply>");
		$xml->addAttribute('encoding', 'UTF-8');
		$parameters = $xml->addChild('message');
		$parameters->addChild("text", $this->getMessage() . "\n(Diese Nachricht kostet sFr. 0." . $this->getCosts() . ")");
		$parameters->addChild("cost", $this->getCosts());

		header("Content-Type: text/xml");
		echo $xml->asXML();
		exit;
	}


	/**
	 * @param int $costs
	 */
	public function setCosts($costs) {
		$this->costs = $costs;
	}


	/**
	 * @return int
	 */
	public function getCosts() {
		return $this->costs;
	}
}

?>