<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
//MST 20131130: I commented out the following line because of problems with ILIAS Modules which use include instead of include_once
//require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ilCtrlMainMenuPlugin.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntryGUI.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntry.php');

/**
 * User interface hook class
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @author            Martin Studer <ms@studer-raimann.ch>
 * @version           2.0.02
 * @ingroup           ServicesUIComponent
 *
 */
class ctrlmmMenuGUI {

	const SIDE_LEFT = 1;
	const SIDE_RIGHT = 2;
	/**
	 * @var ilTemplate
	 */
	protected $html;
	/**
	 * @var int
	 */
	protected $side = self::SIDE_LEFT;
	protected $css_id = '';


	/**
	 * @return mixed
	 */
	public function getCssId() {
		return $this->css_id;
	}


	/**
	 * @param mixed $css_id
	 */
	public function setCssId($css_id) {
		$this->css_id = $css_id;
	}


	/**
	 * @param int $id
	 */
	public function __construct($id = 0) {
		global $tpl;

		$this->pl = ilCtrlMainMenuPlugin::getInstance();
		$this->object = new ctrlmmMenu($id);

		$tpl->addCss($this->pl->getDirectory() . '/templates/css/ctrlmm.css');
		if (ilCtrlMainMenuConfig::get(ilCtrlMainMenuConfig::F_CSS_PREFIX) == 'fb') {
			$tpl->addCss($this->pl->getDirectory() . '/templates/css/fb.css');
		}
		if (ilCtrlMainMenuConfig::get(ilCtrlMainMenuConfig::F_SIMPLE_FORM_VALIDATION)) {
			$tpl->addCss($this->pl->getDirectory() . '/templates/css/forms.css');
			$tpl->addJavaScript($this->pl->getDirectory() . '/templates/js/forms.js');
		}
		if (ilCtrlMainMenuConfig::get(ilCtrlMainMenuConfig::F_DOUBLECLICK_PREVENTION)) {
			$tpl->addCss($this->pl->getDirectory() . '/templates/css/click.css');
			$tpl->addJavaScript($this->pl->getDirectory() . '/templates/js/click.js');
		}
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/class.ctrlmm.php');

		$this->html = $this->pl->getVersionTemplate('tpl.ctrl_menu.html');
		$entry_html = '';
		$replace_full = ilCtrlMainMenuConfig::get(ilCtrlMainMenuConfig::F_REPLACE_FULL_HEADER);
		/**
		 * @var $entry ctrlmmEntry
		 */

		foreach ($this->object->getEntries() as $k => $entry) {
			if ($entry->getType() == ctrlmmMenu::TYPE_SEPARATOR) {
				if ($replace_full) {
					$this->object->setAfterSeparator(true);
				}
				continue;
			}
			if ($this->object->getAfterSeparator() AND $this->getSide() == self::SIDE_LEFT) {
				continue;
			}

			if (!$this->object->getAfterSeparator() AND $this->getSide() == self::SIDE_RIGHT) {
				continue;
			}

			if ($entry->checkPermission()) {
				if ($entry->getId() == 0) {
					$gui_class = ctrlmmEntryInstaceFactory::getInstanceByTypeId($entry->getType())->getGUIObjectClass();
					$entryGui = new $gui_class($entry, $this);
				} else {
					$entryGui = ctrlmmEntryInstaceFactory::getInstanceByEntryId($entry->getId())->getGUIObject();
				}
				$entry_html .= $entryGui->prepareAndRenderEntry($entry->getParent() . '_' . $k);
			}
		}
		$this->html->setVariable('ENTRIES', $entry_html);
		$this->html->setVariable('CSS_PREFIX', ilCtrlMainMenuConfig::get(ilCtrlMainMenuConfig::F_CSS_PREFIX));
		$this->html->setVariable('ID', $this->css_id);

		return $this->html->get();
	}


	/**
	 * @param int $side
	 */
	public function setSide($side) {
		$this->side = $side;
	}


	public function setLeft() {
		$this->setSide(self::SIDE_LEFT);
	}


	public function setRight() {
		$this->setSide(self::SIDE_RIGHT);
	}


	/**
	 * @return int
	 */
	public function getSide() {
		return $this->side;
	}
}

?>
