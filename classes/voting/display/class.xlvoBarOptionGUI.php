<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoBarGUI.php');

class xlvoBarOptionGUI extends xlvoBarGUI
{

    /**
     * @var ilTemplate
     */
    protected $tpl;
    /**
     * @var int
     */
    protected $obj_id;
    /**
     * @var string
     */
    protected $bar_title;

    /**
     * @var string
     */
    protected $option_letter;

    /**
     * @param $obj_id
     * @param $bar_title
     * @param $option_letter
     */
    public function __construct($obj_id, $bar_title, $option_letter)
    {
        //		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/Display/bar.js');

        $this->obj_id = $obj_id;
        $this->option_letter = $option_letter;
        $this->bar_title = $bar_title;
        $this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/tpl.bar_option.html', false, false);
    }


    protected function render()
    {
        $this->tpl->setVariable('OPTION_LETTER', $this->option_letter);
        $this->tpl->setVariable('PERCENT', $this->percentage);
        $this->tpl->setVariable('ID', $this->obj_id);
        $this->tpl->setVariable('TITLE', $this->bar_title);
    }


    /**
     * @return string
     */
    public function getHTML()
    {
        $this->render();

        return $this->tpl->get();
    }


    /**
     * @return int
     */
    public function getPercentage()
    {
        return $this->percentage;
    }


    /**
     * @param int $percentage
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
    }
}