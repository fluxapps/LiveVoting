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

        if(ctrlmm::is50()) {
            $this->gl->setAsDropDown(true);
        }

		$this->setGroupedListContent();

		$this->html = $this->pl->getVersionTemplate('tpl.grouped_list_dropdown.html');

		$this->html->setVariable('TXT_TITLE', $this->entry->getTitle());
		$this->html->setVariable('PREFIX', ilCtrlMainMenuConfig::get(ilCtrlMainMenuConfig::F_CSS_PREFIX));
		if ($this->show_arrow) {
			$this->html->setVariable('ARROW_IMG', ilUtil::getImagePath('mm_down_arrow.png'));
		}
		if ($this->entry->getIcon()) {
			$this->html->setVariable('ICON', $this->entry->getIcon());
		}

		$this->html->setVariable('CONTENT', $this->getContent());
		$this->html->setVariable('ENTRY_ID', $this->getDropdownId());
		$this->html->setVariable('OVERLAY_ID', $this->getDropdownId('ov'));
		$this->html->setVariable('TARGET_REPOSITORY', '_top');

		$list_id = ($this->entry->getListId()!='')? ' id="'.$this->entry->getListId().'"' : '';
		$this->html->setVariable('LIST_ID', $list_id);

		if ($this->entry->isActive()) {
			$this->html->setVariable('MM_CLASS', ilCtrlMainMenuConfig::get(ilCtrlMainMenuConfig::F_CSS_ACTIVE));
			$this->html->setVariable('SEL', '<span class=\'ilAccHidden\'>(' . $lng->txt('stat_selected') . ')</span>');
		} else {
			$this->html->setVariable('MM_CLASS', ilCtrlMainMenuConfig::get(ilCtrlMainMenuConfig::F_CSS_INACTIVE));
		}

		$this->accessKey();
		if(!ctrlmm::is50()) {
			$this->ov = new ilOverlayGUI($this->getDropdownId('ov'));
			$this->ov->setTrigger($this->getDropdownId());
			$this->ov->setAnchor($this->getDropdownId());
			$this->ov->setAutoHide(false);
			$this->ov->add();
		}

		$html = $this->html->get();

		return $html;
	}

	public function getDropdownId($post_fix = 'tr') {
		return 'mm_'.$this->entry->getId().'_'.$post_fix;
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
