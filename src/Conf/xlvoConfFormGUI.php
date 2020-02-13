<?php

namespace LiveVoting\Conf;

use ilCheckboxInputGUI;
use ilFormSectionHeaderGUI;
use ilLiveVotingPlugin;
use ilMultiSelectInputGUI;
use ilNonEditableValueGUI;
use ilNumberInputGUI;
use ilPropertyFormGUI;
use ilSelectInputGUI;
use LiveVoting\Api\xlvoApi;
use LiveVoting\Utils\LiveVotingTrait;
use srag\CustomInputGUIs\LiveVoting\TextInputGUI\TextInputGUI;
use srag\DIC\LiveVoting\DICTrait;
use xlvoConfGUI;

/**
 * Class xlvoConfFormGUI
 *
 * @package LiveVoting\Conf
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoConfFormGUI extends ilPropertyFormGUI
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;
    /**
     * @var xlvoConf
     */
    protected $object;
    /**
     * @var xlvoConfGUI
     */
    protected $parent_gui;


    /**
     * xlvoConfFormGUI constructor.
     *
     * @param xlvoConfGUI $parent_gui
     */
    public function __construct(xlvoConfGUI $parent_gui)
    {
        parent::__construct();

        $this->parent_gui = $parent_gui;
        $this->initForm();
    }


    /**
     *
     */
    protected function initForm()
    {
        $this->setTarget('_top');
        $this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent_gui));
        $this->initButtons();

        $use_shortlink_vote = new ilCheckboxInputGUI($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK_VOTE), xlvoConf::F_ALLOW_SHORTLINK_VOTE);
        $use_shortlink_vote->setInfo($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK_VOTE . '_info') . '<br><br><span class="label label-default">'
            . xlvoConf::REWRITE_RULE_VOTE . '</span><br><br>');

        $shortlink_vote = new TextInputGUI($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK_VOTE_LINK), xlvoConf::F_ALLOW_SHORTLINK_VOTE_LINK);
        $shortlink_vote->setInfo($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK_VOTE_LINK . '_info'));
        $use_shortlink_vote->addSubItem($shortlink_vote);

        $base_url_vote = new TextInputGUI($this->parent_gui->txt(xlvoConf::F_BASE_URL_VOTE), xlvoConf::F_BASE_URL_VOTE);
        $base_url_vote->setInfo($this->parent_gui->txt(xlvoConf::F_BASE_URL_VOTE . '_info'));
        $use_shortlink_vote->addSubItem($base_url_vote);

        $use_shortlink_presenter = new ilCheckboxInputGUI($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK_PRESENTER), xlvoConf::F_ALLOW_SHORTLINK_PRESENTER);
        $use_shortlink_presenter->setInfo($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK_PRESENTER . '_info')
            . '<br><br><span class="label label-default">' . xlvoConf::REWRITE_RULE_PRESENTER . '</span><br><br>');

        $shortlink_presenter = new TextInputGUI($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK_PRESENTER_LINK), xlvoConf::F_ALLOW_SHORTLINK_PRESENTER_LINK);
        $shortlink_presenter->setInfo($this->parent_gui->txt(xlvoConf::F_ALLOW_SHORTLINK_PRESENTER_LINK . '_info'));
        $use_shortlink_presenter->addSubItem($shortlink_presenter);

        $request_frequency = new ilNumberInputGUI($this->parent_gui->txt(xlvoConf::F_REQUEST_FREQUENCY), xlvoConf::F_REQUEST_FREQUENCY);
        $request_frequency->setInfo($this->parent_gui->txt(xlvoConf::F_REQUEST_FREQUENCY . '_info'));
        $request_frequency->allowDecimals(true);
        $request_frequency->setMinValue(xlvoConf::MIN_CLIENT_UPDATE_FREQUENCY, false);
        $request_frequency->setMaxValue(xlvoConf::MAX_CLIENT_UPDATE_FREQUENCY, false);

        //global cache setting
        $global_cache_enabled = new ilCheckboxInputGUI($this->parent_gui->txt(xlvoConf::F_USE_GLOBAL_CACHE), xlvoConf::F_USE_GLOBAL_CACHE);
        $global_cache_enabled->setInfo($this->parent_gui->txt(xlvoConf::F_USE_GLOBAL_CACHE . '_info'));

        // Results API
        $result_api = new ilCheckboxInputGUI($this->parent_gui->txt(xlvoConf::F_RESULT_API), xlvoConf::F_RESULT_API);
        $result_api->setInfo($this->parent_gui->txt(xlvoConf::F_RESULT_API . '_info'));

        $api_type = new ilSelectInputGUI($this->parent_gui->txt(xlvoConf::F_API_TYPE), xlvoConf::F_API_TYPE);
        $api_type->setOptions(array(
            xlvoApi::TYPE_JSON => 'JSON',
            xlvoApi::TYPE_XML  => 'XML',
        ));
        $result_api->addSubItem($api_type);

        $api_token = new ilNonEditableValueGUI();
        $api_token->setTitle($this->parent_gui->txt(xlvoConf::F_API_TOKEN));
        $api_token->setValue(xlvoConf::getApiToken());
        $result_api->addSubItem($api_token);

        // PPT Export
        $ppt_export = new ilCheckboxInputGUI($this->parent_gui->txt(xlvoConf::F_ACTIVATE_POWERPOINT_EXPORT), xlvoConf::F_ACTIVATE_POWERPOINT_EXPORT);
        $ppt_export->setInfo(htmlspecialchars($this->parent_gui->txt(xlvoConf::F_ACTIVATE_POWERPOINT_EXPORT . '_info')) . '<br><br><i>'
            . htmlspecialchars($this->parent_gui->txt(xlvoConf::F_ACTIVATE_POWERPOINT_EXPORT . "_info_manual")) . '</i><ol>'
            . implode("", array_map(function ($step) {
                return '<li>' . htmlspecialchars($this->parent_gui->txt(xlvoConf::F_ACTIVATE_POWERPOINT_EXPORT . "_info_manual_" . $step)) . '</li>';
            }, range(1, 4))) . '</ol>');

        // Use serif font for PIN's
        $use_serif_font_for_pins = new ilCheckboxInputGUI($this->parent_gui->txt(xlvoConf::F_USE_SERIF_FONT_FOR_PINS), xlvoConf::F_USE_SERIF_FONT_FOR_PINS);
        $use_serif_font_for_pins->setInfo($this->parent_gui->txt(xlvoConf::F_USE_SERIF_FONT_FOR_PINS . '_info'));

        //add items to GUI
        $this->addItem($use_shortlink_vote);
        $this->addItem($use_shortlink_presenter);
        $this->addItem($request_frequency);
        $this->addItem($result_api);
        $this->addItem($global_cache_enabled);
        $this->addItem($ppt_export);
        $this->addItem($use_serif_font_for_pins);
    }


    /**
     *
     */
    protected function initButtons()
    {
        $this->addCommandButton(xlvoConfGUI::CMD_UPDATE, $this->parent_gui->txt(xlvoConfGUI::CMD_UPDATE));
        $this->addCommandButton(xlvoConfGUI::CMD_CANCEL, $this->parent_gui->txt(xlvoConfGUI::CMD_CANCEL));
    }


    /**
     *
     */
    public function fillForm()
    {
        $array = array();
        foreach ($this->getItems() as $item) {
            $this->getValuesForItem($item, $array);
        }
        $this->setValuesByArray($array);
    }


    /**
     * @param $item
     * @param $array
     *
     * @internal param $key
     */
    private function getValuesForItem($item, &$array)
    {
        if (self::checkItem($item)) {
            $key = $item->getPostVar();
            $array[$key] = xlvoConf::getConfig($key);
            if (self::checkForSubItem($item)) {
                foreach ($item->getSubItems() as $subitem) {
                    $this->getValuesForItem($subitem, $array);
                }
            }
        }
    }


    /**
     * @return bool
     */
    public function saveObject()
    {
        if (!$this->checkInput()) {
            return false;
        }
        foreach ($this->getItems() as $item) {
            $this->saveValueForItem($item);
        }
        xlvoConf::set(xlvoConf::F_CONFIG_VERSION, xlvoConf::CONFIG_VERSION);

        return true;
    }


    /**
     * @param $item
     */
    private function saveValueForItem($item)
    {
        if (self::checkItem($item)) {
            $key = $item->getPostVar();
            xlvoConf::set($key, $this->getInput($key));
            if (self::checkForSubItem($item)) {
                foreach ($item->getSubItems() as $subitem) {
                    $this->saveValueForItem($subitem);
                }
            }
        }
    }


    /**
     * @param $item
     *
     * @return bool
     */
    public static function checkForSubItem($item)
    {
        return !$item instanceof ilFormSectionHeaderGUI AND !$item instanceof ilMultiSelectInputGUI;
    }


    /**
     * @param $item
     *
     * @return bool
     */
    public static function checkItem($item)
    {
        return !$item instanceof ilFormSectionHeaderGUI && !$item instanceof ilNonEditableValueGUI;
    }
}
