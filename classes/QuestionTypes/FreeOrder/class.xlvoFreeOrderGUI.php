<?php

use LiveVoting\Js\xlvoJs;

/**
 * Class xlvoFreeOrderGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoFreeOrderGUI: xlvoVoter2GUI
 */
class xlvoFreeOrderGUI extends xlvoCorrectOrderGUI {

	public function initJS() {
		xlvoJs::getInstance()->api($this)->name('FreeOrder')->category('QuestionTypes')->addLibToHeader('jquery.ui.touch-punch.min.js')->init();
	}


	/**
	 * @return array
	 */
	public function getButtonInstances() {
		if (!$this->manager->getPlayer()->isShowResults()) {
			return array();
		}
		$states = $this->getButtonsStates();
		$b = \ilLinkButton::getInstance();
		$b->setId(self::BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER);
		if ($states[self::BUTTON_TOTTLE_DISPLAY_CORRECT_ORDER]) {
			$b->setCaption(xlvoGlyphGUI::get('align-left'), false);
		} else {
			$b->setCaption(xlvoGlyphGUI::get('sort-by-attributes-alt'), false);
		}

		//		$t = ilLinkButton::getInstance();
		//		$t->setId(self::BUTTON_TOGGLE_PERCENTAGE);
		//		if ($states[self::BUTTON_TOGGLE_PERCENTAGE]) {
		//			$t->setCaption('%', false);
		//		} else {
		//			$t->setCaption(xlvoGlyphGUI::get('user'), false);
		//		}

		return array( $b );
	}


	/**
	 * @return bool
	 */
	protected function isShowCorrectOrder() {
		return false;
	}
}
