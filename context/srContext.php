<?php
include_once "Services/Context/classes/class.ilContext.php";

class srContext extends ilContext {

	public function __construct() {
		self::init('srContextLvo');
	}

	public static function init($context) {
		include_once('srContextLvo.php');
		self::$class_name = 'srContextLvo';
		self::$type = - 1;
	}
}

?>