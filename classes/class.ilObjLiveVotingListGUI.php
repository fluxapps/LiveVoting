<?php

/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
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

use LiveVoting\Pin\xlvoPin;
use LiveVoting\Utils\LiveVotingTrait;
use srag\DIC\LiveVoting\DICTrait;

/**
 * ListGUI implementation for LiveVoting object plugin. This one
 * handles the presentation in container items (categories, courses, ...)
 * together with the corresponfing ...Access class.
 *
 * PLEASE do not create instances of larger classes here. Use the
 * ...Access class to get DB data and keep it small.
 *
 */
class ilObjLiveVotingListGUI extends ilObjectPluginListGUI {

	use DICTrait;
	use LiveVotingTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
	/**
	 * @var array
	 */
	protected $commands = array();


	/**
	 * Init type
	 */
	public function initType() {
		$this->setType(ilLiveVotingPlugin::PLUGIN_ID);
	}


	/**
	 * Get name of gui class handling the commands
	 */
	public function getGuiClass() {
		return ilObjLiveVotingGUI::class;
	}


	/**
	 * Get commands
	 */
	public function initCommands() {
		$this->static_link_enabled = true;
		$this->delete_enabled = true;
		$this->cut_enabled = true;
		$this->copy_enabled = true;
		$this->subscribe_enabled = true;
		$this->link_enabled = true;
		$this->payment_enabled = false;
		$this->info_screen_enabled = true;
		$this->timings_enabled = false;

		$this->gui_class_name = $this->getGuiClass();

		// general commands array
		$this->commands = array(
			array(
				"permission" => "read",
				"cmd" => ilObjLiveVotingGUI::CMD_SHOW_CONTENT,
				"default" => true
			),
			array(
				"permission" => "write",
				"cmd" => ilObjLiveVotingGUI::CMD_EDIT,
				"txt" => $this->txt("xlvo_edit"),
				"default" => false
			),
		);

		return $this->commands;
	}


	/**
	 * @param string $a_cmd
	 *
	 * @return string
	 */
	public function getCommandFrame($a_cmd) {
		if (!$this->checkCommandAccess("write", $a_cmd, $this->ref_id, $this->type)) {
			return '_blank';
		}

		return parent::getCommandFrame($a_cmd);
	}


	/**
	 * Get item properties
	 *
	 * @return    array        array of property arrays:
	 *                        "alert" (boolean) => display as an alert property (usually in red)
	 *                        "property" (string) => property name
	 *                        "value" (string) => property value
	 */
	public function getProperties() {
		$props = array();

		$props[] = array(
			"alert" => false,
			"property" => $this->txt("voter_pin_input"),
			"value" => xlvoPin::formatPin(xlvoPin::lookupPin($this->obj_id)) // TODO: default.css not loaded
		);

		if (!ilObjLiveVotingAccess::checkOnline($this->obj_id)) {
			$props[] = array(
				"alert" => true,
				"property" => $this->txt("obj_status"),
				"value" => $this->txt("obj_offline")
			);
		}

		return $props;
	}
}
