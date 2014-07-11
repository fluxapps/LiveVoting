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

	/**
	 * @var int
	 */
	protected $ref_id = 1;
	/**
	 * @var int
	 */
	protected $recursive = 0;
	/**
	 * @var int
	 */
	protected $type = ctrlmmMenu::TYPE_REFID;


	/**
	 * @return bool
	 */
	public function isActive() {
		if (! $_GET['ref_id']) {
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
		return ilLink::_getLink($this->getRefId());
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
}