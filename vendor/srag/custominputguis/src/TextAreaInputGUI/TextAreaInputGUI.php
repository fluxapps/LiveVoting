<?php

namespace srag\CustomInputGUIs\TextAreaInputGUI;

use ilTemplate;
use ilTextAreaInputGUI;
use srag\DIC\DICTrait;

/**
 * Class TextInputGUI
 *
 * @package srag\CustomInputGUIs\TextAreaInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class TextAreaInputGUI extends ilTextAreaInputGUI {

	use DICTrait;
	/**
	 * @var string
	 */
	protected $inline_style = '';
	/**
	 * @var int
	 */
	protected $maxlength = 1000;


	/**
	 *
	 */
	public function customPrepare()/*: void*/ {
		$this->addPlugin('latex');
		$this->addButton('latex');
		$this->addButton('pastelatex');
		$this->setUseRte(true);
		$this->setRteTags(array(
			'p',
			'br',
			'b',
			'span',
		));
		$this->usePurifier(true);
		$this->disableButtons(array(
			'charmap',
			'undo',
			'redo',
			'justifyleft',
			'justifycenter',
			'justifyright',
			'justifyfull',
			'anchor',
			'fullscreen',
			'cut',
			'copy',
			'paste',
			'pastetext',
			'formatselect',
		));
	}


	/**
	 * @return string
	 */
	public function render()/*: string*/ {
		$tpl = new ilTemplate(__DIR__ . '/templates/tpl.text_area_helper.html', false, false);
		$this->insert($tpl);
		$tpl->setVariable('INLINE_STYLE', $this->getInlineStyle());

		return $tpl->get();
	}


	/**
	 * @return string
	 */
	public function getInlineStyle()/*: string*/ {
		return $this->inline_style;
	}


	/**
	 * @param string $inline_style
	 */
	public function setInlineStyle(/*string*/
		$inline_style)/*: void*/ {
		$this->inline_style = $inline_style;
	}


	/**
	 * @return int
	 */
	public function getMaxlength()/*: int*/ {
		return $this->maxlength;
	}


	/**
	 * @param int $maxlength
	 */
	public function setMaxlength(/*int*/
		$maxlength)/*: void*/ {
		$this->maxlength = $maxlength;
	}
}
