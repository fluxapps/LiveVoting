<?php

/**
 * Class xlvoJsResponse
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoJsResponse {

	/**
	 * @var mixed
	 */
	protected $data;


	/**
	 * xlvoJsResponse constructor.
	 * @param mixed $data
	 */
	protected function __construct($data) { $this->data = $data; }


	/**
	 * @param $data
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
