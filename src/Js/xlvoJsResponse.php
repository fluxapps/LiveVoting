<?php

namespace LiveVoting\Js;

use ilLiveVotingPlugin;
use srag\DIC\DICTrait;

/**
 * Class xlvoJsResponse
 *
 * @package LiveVoting\Js
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoJsResponse {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	/**
	 * @var mixed
	 */
	protected $data;


	/**
	 * xlvoJsResponse constructor.
	 *
	 * @param mixed $data
	 */
	protected function __construct($data) { $this->data = $data; }


	/**
	 * @param $data
	 *
	 * @return xlvoJsResponse
	 */
	public static function getInstance($data) {
		return new self($data);
	}


	public function send() {
		header('Content-type: application/json');
		echo json_encode($this->data);
		exit;
	}
}
