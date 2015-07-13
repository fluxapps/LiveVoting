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
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntry.php');
@include_once('./Services/Link/classes/class.ilLink.php');
@include_once('./classes/class.ilLink.php');

/**
 * Application class for ctrlmmEntryCtrl Object.
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmEntryRefid extends ctrlmmEntry {
	const PARAM_NAME = 'param_name';
	const PARAM_VALUE = 'param_value';

	/**
	 * @var int
	 */
	protected $ref_id = 1;
	/**
	 * @var int
	 */
	protected $recursive = 0;

	/**
	 * @var array
	 */
	protected $get_params = array();

	/**
	 * @var int
	 */
	//protected $type = ctrlmmMenu::TYPE_REFID;
	public function __construct($primary_key = 0) {
		$this->setType(ctrlmmMenu::TYPE_REFID);

		parent::__construct($primary_key);
	}


	/**
	 * @return bool
	 */
	public function isActive() {
		if (!$_GET['ref_id']) {
			return false;
		} else {

			global $tree;
			/**
			 * @var $tree ilTree
			 */
			if ($this->getRecursive()) {
				if (($_GET['ref_id'] == $this->getRefId() OR
					$tree->isGrandChild($this->getRefId(), $_GET['ref_id']) AND strtolower($_GET['baseClass']) != 'iladministrationgui')
				) {
					return true;
				}
			} else {
				if (($_GET['ref_id'] == $this->getRefId() AND strtolower($_GET['baseClass']) != 'iladministrationgui')
				) {
					return true;
				}
			}

			return false;
		}
	}


	/**
	 * @return string
	 */
	public function getLink() {
		$param_array = array();
		foreach($this->getGetParams() as $entry) {
			if($entry[self::PARAM_NAME] != "") {
				$param_array[$entry[self::PARAM_NAME]] = ctrlmmUserDataReplacer::parse($entry[self::PARAM_VALUE]);
			}
		}

		return ilLink::_getLink($this->getRefId(), '', $param_array);
	}


	/**
	 * @param int $ref_id
	 */
	public function setRefId($ref_id) {
		$this->ref_id = $ref_id;
	}


	/**
	 * @return int
	 */
	public function getRefId() {
		return $this->ref_id;
	}


	/**
	 * @param boolean $target
	 */
	public function setTarget($target) {
		$this->target = $target;
	}


	/**
	 * @return boolean
	 */
	public function getTarget() {
		return $this->target;
	}


	/**
	 * @param int $recursive
	 */
	public function setRecursive($recursive) {
		$this->recursive = $recursive;
	}


	/**
	 * @return int
	 */
	public function getRecursive() {
		return $this->recursive;
	}


	/**
	 * @return array
	 */
	public function getGetParams() {
		return $this->get_params;
	}


	/**
	 * @param array $get_params
	 */
	public function setGetParams($get_params) {
		$this->get_params = $get_params;
	}


}