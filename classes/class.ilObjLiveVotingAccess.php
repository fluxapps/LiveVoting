<?php

/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2001 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/

require_once __DIR__ . '/../vendor/autoload.php';

use LiveVoting\Utils\LiveVotingTrait;
use LiveVoting\Voting\xlvoVotingConfig;
use srag\DIC\LiveVoting\DICTrait;

/**
 *
 * Class ilObjLiveVotingAccess
 *
 * Access/Condition checking for LiveVoting object
 *
 * Please do not create instances of large application classes (like ilObjExample)
 * Write small methods within this class to determin the status.
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version $Id$
 */
class ilObjLiveVotingAccess extends ilObjectPluginAccess {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


	/**
	 * Checks wether a user may invoke a command or not
	 * (this method is called by ilAccessHandler::checkAccess)
	 *
	 * Please do not check any preconditions handled by
	 * ilConditionHandler here. Also don't do usual RBAC checks.
	 *
	 * @param    string $a_cmd        command (not permission!)
	 * @param    string $a_permission permission
	 * @param    int    $a_ref_id     reference id
	 * @param    int    $a_obj_id     object id
	 * @param    string $a_user_id    user id (if not provided, current user is taken)
	 *
	 * @return    boolean        true, if everything is ok
	 */
	public function _checkAccess($a_cmd, $a_permission, $a_ref_id, $a_obj_id, $a_user_id = "") {
		if ($a_user_id == "") {
			$a_user_id = self::dic()->user()->getId();
		}

		switch ($a_permission) {
			case "visible":
			case "read":
				if (!self::checkOnline($a_obj_id)
					&& !self::dic()->access()->checkAccessOfUser($a_user_id, "write", "", $a_ref_id)) {
					return false;
				}
				break;
		}

		return true;
	}


	/**
	 * @param null $ref_id
	 * @param null $user_id
	 *
	 * @return bool
	 */
	public static function hasReadAccess($ref_id = NULL, $user_id = NULL) {
		return self::hasAccess('read', $ref_id, $user_id);
	}


	/**
	 * @param null $ref_id
	 * @param null $user_id
	 *
	 * @return bool
	 */
	public static function hasWriteAccess($ref_id = NULL, $user_id = NULL) {
		return self::hasAccess('write', $ref_id, $user_id);
	}


	/**
	 * @param $obj_id
	 * @param $user_id
	 *
	 * @return bool
	 */
	public static function hasReadAccessForObject($obj_id, $user_id) {
		$refs = ilObject2::_getAllReferences($obj_id);
		foreach ($refs as $ref_id) {
			if (self::hasReadAccess($ref_id, $user_id)) {
				return true;
				break;
			}
		}

		return false;
	}


	/**
	 * @param $obj_id
	 * @param $user_id
	 *
	 * @return bool
	 */
	public static function hasWriteAccessForObject($obj_id, $user_id) {
		$refs = ilObject2::_getAllReferences($obj_id);

		foreach ($refs as $ref_id) {
			if (self::hasWriteAccess($ref_id, $user_id)) {
				return true;
				break;
			}
		}

		return false;
	}


	/**
	 * @param null $ref_id
	 * @param null $user_id
	 *
	 * @return bool
	 */
	public static function hasDeleteAccess($ref_id = NULL, $user_id = NULL) {
		return self::hasAccess('delete', $ref_id, $user_id);
	}


	/**
	 * @param null $ref_id
	 * @param null $user_id
	 *
	 * @return bool
	 */
	public static function hasCreateAccess($ref_id = NULL, $user_id = NULL) {
		return self::hasAccess('create_xlvo', $ref_id, $user_id);
	}


	/**
	 * @param      $permission
	 * @param null $ref_id
	 * @param null $user_id
	 *
	 * @return bool
	 */
	protected static function hasAccess($permission, $ref_id = NULL, $user_id = NULL) {
		$ref_id = $ref_id ? $ref_id : $_GET['ref_id'];
		$user_id = $user_id ? $user_id : self::dic()->user()->getId();
		//		if (!$this->user_id) {
		//			try {
		//				throw new Exception();
		//			} catch (Exception $e) {
		//              $ilLog = $DIC["ilLog"];
		//				$ilLog->write('XLVO ##########################');
		//				$ilLog->write('XLVO ' . xlvoInitialisation::getContext());
		//				$ilLog->write('XLVO ' . $e->getTraceAsString());
		//				$ilLog->write('XLVO ##########################');
		//				return true;
		//			}
		//		}

		//		$ilLog->write('XLVO check permission ' . $permission . ' for user ' .$this->user_id . ' on ref_id ' . $ref_id);

		return self::dic()->access()->checkAccessOfUser($user_id, $permission, '', $ref_id);
	}


	/**
	 * Check online status of example object
	 */
	public static function checkOnline($a_id = NULL) {
		/**
		 * @var xlvoVotingConfig $config
		 */
		$obj_id = $a_id ? $a_id : ilObject2::_lookupObjId($_GET['ref_id']);
		$config = xlvoVotingConfig::find($obj_id);
		if ($config instanceof xlvoVotingConfig) {
			return $config->isObjOnline();
		}

		return false;
	}
}
