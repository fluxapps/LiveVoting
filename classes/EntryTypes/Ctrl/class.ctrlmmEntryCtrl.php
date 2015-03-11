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

/**
 * Application class for ctrlmmEntryCtrl Object.
 *
 * @author         Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version        2.0.02
 */
class ctrlmmEntryCtrl extends ctrlmmEntry {

	const DEBUG = true;
	/**
	 * @var string
	 */
	protected $gui_class = '';
	/**
	 * @var string
	 */
	protected $cmd = '';
	/**
	 * @var string
	 */
	protected $additions = '';
	/**
	 * @var int
	 */
	protected $ref_id = NULL;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;


	/**
	 * @param int $id
	 */
	function __construct($id = 0) {
		global $ilCtrl;

		$this->setType(ctrlmmMenu::TYPE_CTRL);
		$this->restricted = ctrlmmMenu::isOldILIAS();
		/**
		 * @var $ilCtrl ilCtrl
		 */
		$this->ctrl = $ilCtrl;

		parent::__construct($id);
	}


	/**
	 * @return bool
	 */
	public function isActive() {
		if (!$this->isActiveStateCached()) {
			$this->setCachedActiveState(false);
			$classes = array();
			foreach (explode(',', $this->getGuiClass()) as $classname) {
				$classes[] = strtolower($classname);
			}
			foreach ($this->ctrl->getCallHistory() as $class) {
				$strtolower = strtolower($class['class']);
				if (in_array($strtolower, $classes)) {
					$this->setCachedActiveState(true);
					break;
				}
			}
		}

		return $this->getCachedActiveState();
	}


	/**
	 * @return null|string
	 */
	protected function getError() {
		if (!$this->checkCtrl()) {
			return 'ilCtrl-Error';
		}

		return NULL;
	}


	/**
	 * @return bool
	 */
	protected function checkCtrl() {
		$gui_classes = @explode(',', $this->getGuiClass());
		if (ctrlmm::is50()) {
			try {
				$this->ctrl->getLinkTargetByClass($gui_classes, $this->getCmd());
			} catch (Exception $e) {
				return false;
			}
		} else {
			$ctrlTwo = new ilCtrl();

			if (!$ctrlTwo->checkTargetClass($gui_classes)) {
				return false;
			}
		}

		return true;
	}


	/**
	 * @return string
	 */
	public function getLink() {
		if (!$this->checkCtrl()) {
			return NULL;
		}
		$gui_classes = @explode(',', $this->getGuiClass());
		if (ctrlmmMenu::isOldILIAS()) {
			$ctrlTwo = new ilCtrl();
			$ctrlTwo->setTargetScript('ilias.php');
			$a_base_class = $_GET['baseClass'];
			$cmd = $_GET['cmd'];
			$cmdClass = $_GET['cmdClass'];
			$cmdNode = $_GET['cmdNode'];
			$ctrlTwo->initBaseClass($gui_classes[0]);
			$link = $ctrlTwo->getLinkTargetByClass($gui_classes, $this->getCmd());
			$_GET['baseClass'] = $a_base_class;
			$_GET['cmd'] = $cmd;
			$_GET['cmdClass'] = $cmdClass;
			$_GET['cmdNode'] = $cmdNode;
		} else {

			$link = $this->ctrl->getLinkTargetByClass($gui_classes, $this->getCmd());
			if ($this->getAdditions()) {
				$link .= '&' . $this->getAdditions();
			}
			if ($this->getRefId()) {
				$link .= '&ref_id=' . $this->getRefId();
			}
		}

		return $link;
	}


	/**
	 * @param string $cmd
	 */
	public function setCmd($cmd) {
		$this->cmd = $cmd;
	}


	/**
	 * @return string
	 */
	public function getCmd() {
		return $this->cmd;
	}


	/**
	 * @param string $gui_class
	 */
	public function setGuiClass($gui_class) {
		$this->gui_class = $gui_class;
	}


	/**
	 * @return string
	 */
	public function getGuiClass() {
		return $this->gui_class;
	}


	/**
	 * @param string $additions
	 */
	public function setAdditions($additions) {
		$this->additions = $additions;
	}


	/**
	 * @return string
	 */
	public function getAdditions() {
		return $this->additions;
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
}