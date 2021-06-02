<?php

namespace srag\CustomInputGUIs\LiveVoting\StaticHTMLPresentationInputGUI;

use ilFormException;
use ilFormPropertyGUI;
use ilTemplate;
use srag\CustomInputGUIs\LiveVoting\Template\Template;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class StaticHTMLPresentationInputGUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\StaticHTMLPresentationInputGUI
 */
class StaticHTMLPresentationInputGUI extends ilFormPropertyGUI
{

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
    public function __construct(string $title = "")
    {
        parent::__construct($title, "");
    }


    /**
     * @inheritDoc
     */
    public function checkInput() : bool
    {
        return true;
    }


    /**
     * @return string
     */
    public function getHtml() : string
    {
        return $this->html;
    }


    /**
     * @param string $html
     *
     * @return self
     */
    public function setHtml(string $html) : self
    {
        $this->html = $html;

        return $this;
    }


    /**
     * @return string
     */
    public function getValue() : string
    {
        return "";
    }


    /**
     * @param ilTemplate $tpl
     */
    public function insert(ilTemplate $tpl)/*: void*/
    {
        $html = $this->render();

        $tpl->setCurrentBlock("prop_generic");
        $tpl->setVariable("PROP_GENERIC", $html);
        $tpl->parseCurrentBlock();
    }


    /**
     * @return string
     */
    public function render() : string
    {
        $iframe_tpl = new Template(__DIR__ . "/templates/iframe.html");

        $iframe_tpl->setVariableEscaped("URL", $this->getDataUrl());

        return self::output()->getHTML($iframe_tpl);
    }


    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle(/*string*/ $title) : self
    {
        $this->title = $title;

        return $this;
    }


    /**
     * @param string $value
     *
     * @throws ilFormException
     */
    public function setValue(/*string*/ $value)/*: void*/
    {
        //throw new ilFormException("StaticHTMLPresentationInputGUI does not support set screenshots!");
    }


    /**
     * @param array $values
     *
     * @throws ilFormException
     */
    public function setValueByArray(/*array*/ $values)/*: void*/
    {
        //throw new ilFormException("StaticHTMLPresentationInputGUI does not support set screenshots!");
    }


    /**
     * @return string
     */
    protected function getDataUrl() : string
    {
        return "data:text/html;charset=UTF-8;base64," . base64_encode($this->html);
    }
}
