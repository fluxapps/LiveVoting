<?php

namespace srag\CustomInputGUIs\MultiSelectSearchInputGUI;

use ilUtil;

/**
 * Class MultiSelectSearchInput2GUI
 *
 * TODO: Merge this class with MultiSelectSearchInput2GUI - almost identical
 *
 * @package srag\CustomInputGUIs\MultiSelectSearchInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 */
class MultiSelectSearchInput2GUI extends MultiSelectSearchInputGUI {

	/**
	 * @var string
	 */
	protected $placeholder = "";


	/**
	 * @return array
	 */
	public function getValue()/*: array*/ {
		$val = parent::getValue();
		if (is_array($val)) {
			return $val;
		} elseif (!$val) {
			return array();
		} else {
			return explode(',', $val);
		}
	}


	/**
	 * @return array
	 */
	public function getSubItems()/*: array*/ {
		return array();
	}


	/**
	 * @return string
	 */
	public function getContainerType()/*: string*/ {
		return 'crs';
	}


	/**
	 * @return string
	 */
	public function render()/*: string*/ {
		$tpl = $this->getInputTemplate();
		$json_values = $this->getValueAsJson();
		$values = $this->getValue();
		$options = $this->getOptions();

		$tpl->setVariable('POST_VAR', $this->getPostVar());
		$tpl->setVariable('ID', $this->stripLastStringOccurrence($this->getPostVar(), "[]"));
		$tpl->setVariable('ESCAPED_ID', $this->escapePostVar($this->getPostVar()));
		$tpl->setVariable('WIDTH', $this->getWidth());
		$tpl->setVariable('PRELOAD', $json_values);
		$tpl->setVariable('HEIGHT', $this->getHeight());
		$tpl->setVariable('PLACEHOLDER', $this->getPlaceholder());
		$tpl->setVariable('MINIMUM_INPUT_LENGTH', $this->getMinimumInputLength());
		$tpl->setVariable('CONTAINER_TYPE', $this->getContainerType());
		$tpl->setVariable('Class', $this->getCssClass());

		if (isset($this->ajax_link)) {
			$tpl->setVariable('AJAX_LINK', $this->getAjaxLink());
		}

		if ($this->getDisabled()) {
			$tpl->setVariable('ALL_DISABLED', 'disabled=\'disabled\'');
		}

		if ($options) {
			foreach ($options as $option_value => $option_text) {
				$tpl->setCurrentBlock('item');
				if ($this->getDisabled()) {
					$tpl->setVariable('DISABLED', ' disabled=\'disabled\'');
				}
				if (in_array($option_value, $values)) {
					$tpl->setVariable('SELECTED', 'selected');
				}

				$tpl->setVariable('VAL', ilUtil::prepareFormOutput($option_value));
				$tpl->setVariable('TEXT', $option_text);
				$tpl->parseCurrentBlock();
			}
		}

		return $tpl->get();
	}


	/**
	 * @return string
	 */
	protected function getValueAsJson()/*: string*/ {
		return json_encode(array());
	}


	/**
	 * @param string $postVar
	 *
	 * @return string
	 */
	protected function escapePostVar(/*string*/
		$postVar)/*: string*/ {
		$postVar = $this->stripLastStringOccurrence($postVar, "[]");
		$postVar = str_replace("[", '\\\\[', $postVar);
		$postVar = str_replace("]", '\\\\]', $postVar);

		return $postVar;
	}


	/**
	 * @param string $text
	 * @param string $string
	 *
	 * @return string
	 */
	private function stripLastStringOccurrence(/*string*/
		$text, /*string*/
		$string)/*: string*/ {
		$pos = strrpos($text, $string);
		if ($pos !== false) {
			$text = substr_replace($text, "", $pos, strlen($string));
		}

		return $text;
	}


	/**
	 * @return string
	 */
	public function getPlaceholder()/*: string*/ {
		return $this->placeholder;
	}


	/**
	 * @param string $placeholder
	 */
	public function setPlaceholder(/*string*/
		$placeholder)/*: void*/ {
		$this->placeholder = $placeholder;
	}
}
