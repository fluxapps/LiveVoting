<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoBarGUI.php');

class xlvoBarPercentageGUI extends xlvoBarGUI
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
     * @var int
     */
    protected $percentage = 0;
    /**
     * @var string
     */
    protected $bar_title;

    /**
     * @param int $obj_id
     * @param int $percentage
     */
    public function __construct($obj_id, $bar_title, $percentage)
    {
        //		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/Display/bar.js');

        $this->obj_id = $obj_id;
        $this->percentage = $percentage;
        $this->bar_title = $bar_title;
        $this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/tpl.bar_percentage.html', false, false);
    }


    protected function render()
    {
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