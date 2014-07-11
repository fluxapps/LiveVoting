<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Entry/class.ctrlmmEntryGUI.php');
require_once('./Services/UIComponent/GroupedList/classes/class.ilGroupedListGUI.php');
require_once('./Services/Accessibility/classes/class.ilAccessKey.php');
require_once('./Services/UIComponent/Overlay/classes/class.ilOverlayGUI.php');

/**
 * ctrlmmEntryGroupedListDropdownGUI
 *
 * @author  Timon Amstutz <timon.amstutz@ilub.unibe.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.02
 *
 */
abstract class ctrlmmEntryGroupedListDropdownGUI extends ctrlmmEntryGUI {

	/**
	 * @var bool
	 */
	protected $show_arrow = true;
	/**
	 * @var ilGroupedListGUI
	 */
	protected $gl = NULL;
	/**
	 * @var ilOverlayGUI
	 */
	protected $ov = NULL;
	/**
	 * @var ilTemplate
	 */
	protected $html;


	/**
	 * @return string
	 */
	public function renderEntry() {
		global $lng;

		$this->gl = new ilGroupedListGUI();
		$this->setGroupedListContent();

		$this->html = $this->pl->getTemplate('tpl.grouped_list_dropdown.html');

		$this->html->setVariable('TXT_TITLE', $this->entry->getTitle());
		$this->html->setVariable('PREFIX', ilCtrlMainMenuPlugin::getConf()->getCssPrefix());
		if ($this->show_arrow) {
			$this->html->setVariable('ARROW_IMG', ilUtil::getImagePath('mm_down_arrow.png'));
		}
		if ($this->entry->getIcon()) {
			$this->html->setVariable('ICON', $this->entry->getIcon());
		}

		$this->html->setVariable('CONTENT', $this->getContent());
		$this->html->setVariable('ENTRY_ID', $this->entry->getId());
		$this->html->setVariable('TARGET_REPOSITORY', '_top');

		if ($this->entry->isActive()) {
			$this->html->setVariable('MM_CLASS', ilCtrlMainMenuPlugin::getConf()->getCssActive());
			$this->html->setVariable('SEL', '<span class=\'ilAccHidden\'>(' . $lng->txt('stat_selected') . ')</span>');
		} else {
			$this->html->setVariable('MM_CLASS', ilCtrlMainMenuPlugin::getConf()->getCssInactive());
		}

		$this->accessKey();

		$this->ov = new ilOverlayGUI('mm_' . $this->entry->getId() . '_ov');
		$this->ov->setTrigger('mm_' . $this->entry->getId() . '_tr');
		$this->ov->setAnchor('mm_' . $this->entry->getId() . '_tr');
		$this->ov->setAutoHide(false);
		$this->ov->add();

		$html = $this->html->get();

		return $html;
	}


	/**
	 * Render main menu entry
	 *
	 * @param
	 *
	 * @return html
	 */
	abstract protected function setGroupedListContent();


	protected function accessKey() {
	}


	/**
	 * @return string
	 */
	protected function getContent() {
		return $this->gl->getHTML();
	}
}

?>
