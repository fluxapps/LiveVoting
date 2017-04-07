<?php
namespace LiveVoting\Api;

use LiveVoting\Conf\xlvoConf;
use LiveVoting\Pin\xlvoPin;
use LiveVoting\Results\xlvoResults;
use LiveVoting\Player\xlvoPlayerException;
use LiveVoting\Round\xlvoRound;
use LiveVoting\Voting\xlvoVotingManager2;

/**
 * Class xlvoApi
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoApi {

	const TYPE_JSON = 1;
	const TYPE_XML = 2;
	/**
	 * @var int
	 */
	protected $type = self::TYPE_XML;
	/**
	 * @var string
	 */
	protected $token = '';
	/**
	 * @var xlvoPin
	 */
	protected $pin;
	/**
	 * @var \stdClass
	 */
	protected $data;


	/**
	 * xlvoApi constructor.
	 *
	 * @param \LiveVoting\Pin\xlvoPin $pin
	 * @param string $token
	 */
	public function __construct(xlvoPin $pin, $token) {
		$this->pin = $pin;
		$this->token = $token;
		$this->initType();
		$this->check();

		$manager = new xlvoVotingManager2($this->pin->getPin());
		$title = \ilObject2::_lookupTitle($manager->getObjId());
		$data = new \stdClass();
		$data->Info = new \stdClass();
		$data->Info->Title = $title;
		$latestRound = xlvoRound::getLatestRound($manager->getObjId());
		$data->Info->Round = $latestRound->getRoundNumber();
		$data->Info->RoundId = $latestRound->getId();
		$data->Info->Pin = $pin->getPin();
		$data->Info->Date = date(DATE_ISO8601);
		$data->Votings = array();

		$xlvoResults = new xlvoResults($manager->getObjId(), $latestRound->getId());

		foreach ($manager->getAllVotings() as $xlvoVoting) {
			$stdClass = $xlvoVoting->_toJson();
			$stdClass->Voters = array();

			foreach ($xlvoResults->getData(array( 'voting' => $xlvoVoting->getId() )) as $item) {
				$Voter = new \stdClass();
				$Voter->Identifier = $item['participant'];
				$Voter->AnswerIds = $item['answer_ids'];
				$Voter->AnswerText = $item['answer'];

				$stdClass->Voters[] = $Voter;
			}

			$data->Votings[$xlvoVoting->getPosition()] = $stdClass;
		}

		$this->data = $data;
	}


	protected function check() {
		xlvoPin::checkPin($this->getPin()->getPin());

		if (!xlvoConf::getConfig(xlvoConf::F_RESULT_API)) {
			throw new xlvoPlayerException('API not configured', 3);
		}
		if ($this->getToken() !== xlvoConf::getApiToken()) {
			throw new xlvoPlayerException('wrong API token', 4);
		}
	}


	/**
	 * @param \DOMElement|\DOMNode $dom
	 * @param $key
	 * @param $data
	 * @return mixed
	 */
	protected function appendXMLElement($dom, $key, $data) {
		$return = $dom;
		switch (true) {
			case ($data instanceof \stdClass):
				$newdom = $dom->appendChild(new \DOMElement($key));
				foreach ($data as $k => $v) {
					$this->appendXMLElement($newdom, $k, $v);
				}
				break;
			case (is_array($data));
				$newdom = $dom->appendChild(new \DOMElement($key));
				foreach ($data as $k => $v) {
					$this->appendXMLElement($newdom, rtrim($key, "s"), $v);
				}
				break;
			default:
				$dom->appendChild(new \DOMElement($key))->appendChild(new \DOMCdataSection($data));
				break;
		}

		return $return;
	}


	public function send() {
		switch ($this->type) {
			case self::TYPE_JSON:
				header('Content-Type: application/json');
				echo json_encode($this->data);
				break;
			case self::TYPE_XML;
				$domxml = new \DOMDocument('1.0', 'UTF-8');
				$domxml->preserveWhiteSpace = false;
				$domxml->formatOutput = true;
				$this->appendXMLElement($domxml, 'LiveVotingResults', $this->data);

				header('Content-Type: application/xml');
				echo $domxml->saveXML();
				break;
		}
	}


	/**
	 * @return string
	 */
	public function getToken() {
		return $this->token;
	}


	/**
	 * @param string $token
	 */
	public function setToken($token) {
		$this->token = $token;
	}


	/**
	 * @return \LiveVoting\Pin\xlvoPin
	 */
	public function getPin() {
		return $this->pin;
	}


	/**
	 * @param \LiveVoting\Pin\xlvoPin $pin
	 */
	public function setPin($pin) {
		$this->pin = $pin;
	}


	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param int $type
	 */
	public function setType($type) {
		$this->type = $type;
	}


	/**
	 * @return \stdClass
	 */
	public function getData() {
		return $this->data;
	}


	/**
	 * @param \stdClass $data
	 */
	public function setData($data) {
		$this->data = $data;
	}


	protected function initType() {
		$type = xlvoConf::getConfig(xlvoConf::F_API_TYPE);
		$this->setType($type ? $type : self::TYPE_JSON);
	}
}
