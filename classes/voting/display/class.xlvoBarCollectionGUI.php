<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoBarGUI.php');

class xlvoBarCollectionGUI
{


    /**
     * @var ilTemplate
     */
    protected $tpl;
    /**
     * @var xlvoVoting
     */
    protected $voting;


    /**
     * @param xlvoVoting $voting
     */
    public function __construct(xlvoVoting $voting)
    {
        //		$tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/Display/bar.js');

        $this->voting = $voting;
        $this->tpl = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/display/tpl.bar_collection.html', false, false);
    }


    protected function render()
    {

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
     * @param $bar_gui xlvoBarGUI
     */
    public function addBar(xlvoBarGUI $bar_gui)
    {
        if($bar_gui instanceof xlvoBarGUI) {
            $this->tpl->setCurrentBlock('bar');
            $this->tpl->setVariable('BAR', $bar_gui->getHTML());
            $this->tpl->parseCurrentBlock();
        }
    }


}