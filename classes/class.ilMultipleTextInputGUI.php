<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Form/classes/class.ilCustomInputGUI.php");
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Config/class.xlvoConfig.php');
/**
 * Class ilMultipleTextInputGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 * @version $Id:
 */
require_once('./Services/Form/classes/class.ilSubEnabledFormPropertyGUI.php');

class ilMultipleTextInputGUI extends ilCustomInputGUI {

	/**
	 * @var array
	 */
	protected $values;
	/**
	 * @var string
	 */
	protected $placeholder;
	/**
	 * @var bool
	 */
	protected $disableOldFields;


	public function __construct($title, $post_var, $placeholder) {
		parent::__construct($title, $post_var);
		$this->placeholder = $placeholder;
	}


	public function getHtml() {
		return $this->buildHTML();
	}


	private function buildHTML() {
		$pl = ilLiveVotingPlugin::getInstance();
		$tpl = $pl->getTemplate("tpl.multiple_input.html");
//		if (xlvoConfig::is50()) {
//			$tpl = $pl->getTemplate("tpl.multiple_input.html");
//		} else {
//			$tpl = $pl->getTemplate("tpl.multiple_input_50.html");
//		}
//
		$tpl->setCurrentBlock("title");
		//		$tpl->setVariable("CSS_PATH", $pl->getStyleSheetLocation("content.css"));

		if (xlvoConfig::is50()) {
//			ilUtil::sendInfo(ilGlyphGUI::get(ilGlyphGUI::ADD));
//			$ilGlyphGUI = new ilGlyphGUI();


			$tpl->setVariable("X_IMAGE_PATH", $pl->getImagePath("x_image.png"));
		} else {
			$tpl->setVariable("X_IMAGE_PATH", $pl->getImagePath("x_image.png"));
		}

		$tpl->setVariable("PLACEHOLDER", $this->placeholder);
		$tpl->setVariable("POSTVAR", $this->getPostVar());
		$tpl->parseCurrentBlock();

		$tpl->touchBlock("lvo_options_start");

		foreach ($this->values as $id => $value) {
			$tpl->setCurrentBlock("lvo_option");
			$tpl->setVariable("OPTION_ID", $this->getPostVar() . "[" . $id . "]");
			$tpl->setVariable("OPTION_VALUE", $value);
			$tpl->setVariable("OPTION_CLASS", "lvo_option");
			$tpl->setVariable("PLACEHOLDER_CLASS", "");
			$tpl->setVariable("PLACEHOLDER", "");
			$tpl->setVariable("X_DISPLAY", "float");
			$tpl->setVariable("DISABLED", "disabled");
			$tpl->setVariable("X_IMAGE_PATH", $pl->getImagePath("x_image.png"));
			$tpl->parseCurrentBlock();
		}

		$tpl->setCurrentBlock("lvo_option");
		$tpl->setVariable("OPTION_ID", $this->getPostVar() . "[new0]");
		$tpl->setVariable("OPTION_TITLE", "");
		$tpl->setVariable("OPTION_CLASS", "lvo_new_option");
		$tpl->setVariable("PLACEHOLDER", "placeholder = '" . $this->placeholder . "'");
		$tpl->setVariable("PLACEHOLDER_CLASS", "placeholder");
		$tpl->setVariable("X_IMAGE_PATH", $pl->getImagePath("x_image.png"));
		$tpl->setVariable("X_DISPLAY", "none");
		$tpl->parseCurrentBlock();

		$tpl->touchBlock("lvo_options_end");

		return $tpl->get();
	}


	/**
	 * @param $value array form $value[$postvar] = array(id, title)
	 */
	public function setValueByArray($value) {
		parent::setValueByArray($value);
		$this->values = is_array($value[$this->getPostVar()]) ? $value[$this->getPostVar()] : array();
	}


	/**
	 * @param boolean $disableOldFields
	 */
	public function setDisableOldFields($disableOldFields) {
		$this->disableOldFields = $disableOldFields;
	}


	/**
	 * @return boolean
	 */
	public function getDisableOldFields() {
		return $this->disableOldFields;
	}


	/**
	 * @return bool
	 */
	public function checkInput() {
		return true;
	}
}

?>