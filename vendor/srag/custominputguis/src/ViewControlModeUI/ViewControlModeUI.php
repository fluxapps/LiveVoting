<?php

namespace srag\CustomInputGUIs\LiveVoting\ViewControlModeUI;

use ilSession;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class ViewControlModeUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\ViewControlModeUI
 */
class ViewControlModeUI
{

    use DICTrait;

    const CMD_HANDLE_BUTTONS = "ViewControlModeUIHandleButtons";
    /**
     * @var array
     */
    protected $buttons = [];
    /**
     * @var string
     */
    protected $default_active_id = "";
    /**
     * @var string
     */
    protected $id = "";
    /**
     * @var string
     */
    protected $link = "";


    /**
     * ViewControlModeUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @return string
     */
    public function getActiveId() : string
    {
        $active_id = ilSession::get(self::CMD_HANDLE_BUTTONS . "_" . $this->id);

        if ($active_id === null || !isset($this->buttons[$active_id])) {
            return $active_id = $this->default_active_id;
        }

        return $active_id;
    }


    /**
     *
     */
    public function handleButtons()/*: void*/
    {
        $active_id = filter_input(INPUT_GET, self::CMD_HANDLE_BUTTONS);

        ilSession::set(self::CMD_HANDLE_BUTTONS . "_" . $this->id, $active_id);

        self::dic()->ctrl()->redirectToURL(ilSession::get(self::CMD_HANDLE_BUTTONS . "_" . $this->id . "_url"));
    }


    /**
     * @return string
     */
    public function render() : string
    {
        ilSession::set(self::CMD_HANDLE_BUTTONS . "_" . $this->id . "_url", $_SERVER["REQUEST_URI"]);

        $actions = [];

        foreach ($this->buttons as $id => $txt) {
            $actions[$txt] = $this->link . "&" . self::CMD_HANDLE_BUTTONS . "=" . $id;
        }

        return self::output()->getHTML(self::dic()->ui()->factory()->viewControl()->mode($actions, "")
            ->withActive($this->buttons[$this->getActiveId()]));
    }


    /**
     * @param array $buttons
     *
     * @return self
     */
    public function withButtons(array $buttons) : self
    {
        $this->buttons = $buttons;

        return $this;
    }


    /**
     * @param string $default_active_id
     *
     * @return self
     */
    public function withDefaultActiveId(string $default_active_id) : self
    {
        $this->default_active_id = $default_active_id;

        return $this;
    }


    /**
     * @param string $id
     *
     * @return self
     */
    public function withId(string $id) : self
    {
        $this->id = $id;

        return $this;
    }


    /**
     * @param string $link
     *
     * @return self
     */
    public function withLink(string $link) : self
    {
        $this->link = $link;

        return $this;
    }
}
