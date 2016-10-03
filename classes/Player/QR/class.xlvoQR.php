<?php

namespace LiveVoting\Player\QR;
use Endroid\QrCode\QrCode;

/**
 * Class xlvoQR
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoQR {

	/**
	 * @param $content
	 * @param $size
	 * @return string
	 */
	public static function getImageDataString($content, $size) {
		$qrCodeLarge = new QrCode($content);
		$qrCodeLarge->setErrorCorrection('high');
		$qrCodeLarge->setForegroundColor(array(
			'r' => 0,
			'g' => 0,
			'b' => 0,
			'a' => 0,
		));
		$qrCodeLarge->setBackgroundColor(array(
			'r' => 255,
			'g' => 255,
			'b' => 255,
			'a' => 0,
		));
		$qrCodeLarge->setPadding(10);
		$qrCodeLarge->setSize($size);

		return $qrCodeLarge->getDataUri();
	}
}