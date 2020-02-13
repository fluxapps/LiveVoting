<?php

namespace LiveVoting\Display\Bar;

/**
 * Class xlvoBarInfoGUI
 *
 * @package LiveVoting\Display\Bar
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoBarInfoGUI extends xlvoAbstractBarGUI implements xlvoGeneralBarGUI
{

    /**
     * @var string
     */
    protected $value;
    /**
     * @var string
     */
    protected $label;


    /**
     * xlvoBarInfoGUI constructor.
     *
     * @param string $label
     * @param string $value
     */
    public function __construct($label, $value)
    {
        parent::__construct();
        $this->label = $label;
        $this->value = $value;
    }


    /**
     *
     */
    protected function render()
    {
        parent::render();
        $this->tpl->setVariable('FREE_INPUT', $this->label . ": " . $this->value);
    }


    /**
     * @return string
     */
    public function getHTML()
    {
        $this->render();

        return $this->tpl->get();
    }
}
