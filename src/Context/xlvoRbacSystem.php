<?php

namespace LiveVoting\Context;

use ilLiveVotingPlugin;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoRbacSystem
 *
 * Live voting rbac system stub.
 *
 * @package LiveVoting\Context
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @since   3.6.1
 */
class xlvoRbacSystem {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	/**
	 * This is only a stub of the check access method which returns always false.
	 *
	 * @param    string  $a_operations one or more operations, separated by commas (i.e.: visible,read,join)
	 * @param    integer $a_ref_id     the child_id in tree (usually a reference_id, no object_id !!)
	 * @param    string  $a_type       the type definition abbreviation (i.e.: frm,grp,crs)
	 *
	 * @return    boolean                    returns true if ALL passed operations are given, otherwise false
	 */
	public function checkAccess($a_operations, $a_ref_id, $a_type = "") {
		return false;
	}
}
