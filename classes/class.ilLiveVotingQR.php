<?php
require_once('Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/lib/phpqrcode/qrlib.php');

/**
 * Class ilLiveVotingQR
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilLiveVotingQR {

	/**
	 * @var string
	 */
	protected $data;


	/**
	 * @param $data
	 *
	 * @return string
	 */
	public static function getQRasBase64($data) {
		ob_start();

		$style = array(
			'border' => true,
			'padding' => 4,
			'fgcolor' => array( 0, 0, 0 ),
			'bgcolor' => false, //array(255,255,255)
		);

		QRcode::png($data['str'], false, QR_ECLEVEL_L, $data['size'], 4);
		$string = ob_get_contents();
		ob_clean();

		return base64_encode($string);
	}
}

?>