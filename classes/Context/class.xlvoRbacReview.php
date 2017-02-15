<?php
namespace LiveVoting\Context;

/**
 * Class xlvoRbacReview
 *
 * This mocks ilRbacReview in PIN Context (bc of ilObjMediaObject in Text)
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoRbacReview {

	/**
	 * @param $a_rol_id
	 * @return array
	 */
	public function assignedUsers($a_rol_id) {
		return array();
	}


	/**
	 * @param $user_id
	 * @return array
	 */
	public function assignedGlobalRoles($user_id) {
		return array();
	}
}

