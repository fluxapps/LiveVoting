<?php
/**
 * Class xlvoDummyUser
 * Dummy user which only simulates required functionally for the ilHelpGUI class.
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */

namespace LiveVoting\Context;

class xlvoDummyUser {

	const LANGUAGE_CODE = "de";


	/**
	 * Returns the language of the user.
	 * This dummy only returns statically the "de" language code
	 * because no other help packages are available atm. (27.10.2016)
	 *
	 * @return string returns the language code "de" without the quotes.
	 */
	public function getLanguage() {
		return self::LANGUAGE_CODE;
	}


	/**
	 * @return int
	 */
	public function getId() {
		return 13;
	}


	/**
	 * This dummy method returns statically false.
	 *
	 * @param string $preference Preference name which will be ignored by this dummy function.
	 *
	 * @return bool         Returns constant false.
	 */
	public function getPref($preference) {
		return false;
	}
}