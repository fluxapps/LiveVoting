<?php

namespace srag\CustomInputGUIs\StaticHTMLPresentationInputGUI;

use ilFormException;
use ilFormPropertyGUI;
use ilTemplate;
use srag\DIC\DICTrait;

/**
 * Class StaticHTMLPresentationInputGUI
 *
 * @package srag\CustomInputGUIs\StaticHTMLPresentationInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class StaticHTMLPresentationInputGUI extends ilFormPropertyGUI {

	use DICTrait;
	/**
	 * @var string
	 */
	protected $html = "";


	/**
	 * StaticHTMLPresentationInputGUI constructor
	 *
	 * @param string $title
	 */
	public function __construct(string $title = "") {
		parent::__construct($title, "");
	}


	/**
	 * @return bool
	 */
	public function checkInput(): bool {
		return true;
	}


	/**
	 * @return string
	 */
	protected function getDataUrl(): string {
		return "data:text/html;base64," . base64_encode($this->html);
	}


	/**
	 * @return string
	 */
	public function getHtml(): string {
		return $this->html;
	}


	/**
	 * @return string
	 */
	public function getValue(): string {
		return "";
	}


	/**
	 * @param ilTemplate $tpl
	 */
	public function insert(ilTemplate $tpl) /*: void*/ {
		$html = $this->render();

		$tpl->setCurrentBlock("prop_generic");
		$tpl->setVariable("PROP_GENERIC", $html);
		$tpl->parseCurrentBlock();
	}


	/**
	 * @return string
	 */
	public function render(): string {
		$iframe_tpl = new ilTemplate(__DIR__ . "/templates/iframe.html", true, true);

		$iframe_tpl->setVariable("URL", $this->getDataUrl());

		return $iframe_tpl->get();
	}


	/**
	 * @param string $html
	 *
	 * @return self
	 */
	public function setHtml(string $html): self {
		$this->html = $html;

		return $this;
	}


	/**
	 * @param string $title
	 *
	 * @return self
	 */
	public function setTitle(/*string*/
		$title): self {
		$this->title = $title;

		return $this;
	}


	/**
	 * @param string $value
	 *
	 * @throws ilFormException
	 */
	public function setValue(/*string*/
		$value)/*: void*/ {
		//throw new ilFormException("StaticHTMLPresentationInputGUI does not support set screenshots!");
	}


	/**
	 * @param array $values
	 *
	 * @throws ilFormException
	 */
	public function setValueByArray(/*string*/
		$values)/*: void*/ {
		//throw new ilFormException("StaticHTMLPresentationInputGUI does not support set screenshots!");
	}
}
