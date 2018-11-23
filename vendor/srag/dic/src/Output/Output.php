<?php

namespace srag\DIC\LiveVoting\Output;

use ILIAS\UI\Component\Component;
use ilTemplate;
use JsonSerializable;
use srag\DIC\LiveVoting\DICTrait;
use srag\DIC\LiveVoting\Exception\DICException;
use stdClass;

/**
 * Class Output
 *
 * @package srag\DIC\LiveVoting\Output
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Output implements OutputInterface {

	use DICTrait;


	/**
	 * @inheritdoc
	 */
	public function getHTML($value)/*: string*/ {
		if (is_array($value)) {
			$html = "";
			foreach ($value as $gui) {
				$html .= $this->getHTML($gui);
			}
		} else {
			switch (true) {
				// HTML
				case (is_string($value)):
					$html = $value;
					break;

				// GUI instance
				case method_exists($value, "getHTML"):
					$html = $value->getHTML();
					break;
				case method_exists($value, "render"):
					$html = $value->render();
					break;

				// Template instance
				case ($value instanceof ilTemplate):
					$html = $value->get();
					break;

				// Component instance
				case ($value instanceof Component):
					$html = self::dic()->ui()->renderer()->render($value);
					break;

				// Not supported!
				default:
					throw new DICException("Class " . get_class($value) . " is not supported for output!");
					break;
			}
		}

		return strval($html);
	}


	/**
	 * @inheritdoc
	 */
	public function output($value, /*bool*/
		$main = true)/*: void*/ {
		$html = $this->getHTML($value);

		if (self::dic()->ctrl()->isAsynch()) {
			echo $html;
		} else {
			if ($main) {
				self::dic()->mainTemplate()->getStandardTemplate();
			}
			self::dic()->mainTemplate()->setContent($html);
			self::dic()->mainTemplate()->show();
		}

		exit;
	}


	/**
	 * @inheritdoc
	 */
	public function outputJSON($value)/*: void*/ {
		switch (true) {
			case (is_string($value)):
			case (is_int($value)):
			case (is_double($value)):
			case (is_bool($value)):
			case (is_array($value)):
			case ($value instanceof stdClass):
			case ($value === NULL):
			case ($value instanceof JsonSerializable):
				$value = json_encode($value);

				header("Content-Type: application/json; charset=utf-8");

				echo $value;

				break;

			default:
				throw new DICException(get_class($value) . " is not a valid JSON value!");
				break;
		}

		exit;
	}
}
