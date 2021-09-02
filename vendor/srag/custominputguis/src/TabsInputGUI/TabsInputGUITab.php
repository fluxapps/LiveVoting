<?php

namespace srag\CustomInputGUIs\LiveVoting\TabsInputGUI;

use ilFormPropertyGUI;
use srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Items\Items;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class TabsInputGUITab
 *
 * @package srag\CustomInputGUIs\LiveVoting\TabsInputGUI
 */
class TabsInputGUITab
{

    use DICTrait;

    /**
     * @var bool
     */
    protected $active = false;
    /**
     * @var string
     */
    protected $info = "";
    /**
     * @var ilFormPropertyGUI[]
     */
    protected $inputs = [];
    /**
     * @var ilFormPropertyGUI[]|null
     */
    protected $inputs_generated = null;
    /**
     * @var string
     */
    protected $post_var = "";
    /**
     * @var string
     */
    protected $title = "";


    /**
     * TabsInputGUITab constructor
     *
     * @param string $title
     * @param string $post_var
     */
    public function __construct(string $title = "", string $post_var = "")
    {
        $this->title = $title;
        $this->post_var = $post_var;
    }


    /**
     *
     */
    public function __clone()
    {
        if ($this->inputs_generated !== null) {
            $this->inputs_generated = array_map(function (ilFormPropertyGUI $input) : ilFormPropertyGUI {
                return clone $input;
            }, $this->inputs_generated);
        }
    }


    /**
     * @param ilFormPropertyGUI $input
     */
    public function addInput(ilFormPropertyGUI $input) : void
    {
        $this->inputs[] = $input;
        $this->inputs_generated = null;
    }


    /**
     * @return string
     */
    public function getInfo() : string
    {
        return $this->info;
    }


    /**
     * @param string $info
     */
    public function setInfo(string $info) : void
    {
        $this->info = $info;
    }


    /**
     * @param string $post_var
     * @param array  $init_value
     *
     * @return ilFormPropertyGUI[]
     */
    public function getInputs(string $post_var, array $init_value) : array
    {
        if ($this->inputs_generated === null) {
            $this->inputs_generated = [];

            foreach ($this->inputs as $input) {
                $input = clone $input;

                $org_post_var = $input->getPostVar();

                if (is_array($init_value[$this->post_var]) && isset($init_value[$this->post_var][$org_post_var])) {
                    Items::setValueToItem($input, $init_value[$this->post_var][$org_post_var]);
                }

                $input->setPostVar($post_var . "[" . $this->post_var . "][" . $org_post_var . "]");

                $this->inputs_generated[$org_post_var] = $input;
            }
        }

        return $this->inputs_generated;
    }


    /**
     * @param ilFormPropertyGUI[] $inputs
     */
    public function setInputs(array $inputs) : void
    {
        $this->inputs = $inputs;
        $this->inputs_generated = null;
    }


    /**
     * @return string
     */
    public function getPostVar() : string
    {
        return $this->post_var;
    }


    /**
     * @param string $post_var
     */
    public function setPostVar(string $post_var) : void
    {
        $this->post_var = $post_var;
    }


    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }


    /**
     * @param string $title
     */
    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }


    /**
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->active;
    }


    /**
     * @param bool $active
     */
    public function setActive(bool $active) : void
    {
        $this->active = $active;
    }
}

