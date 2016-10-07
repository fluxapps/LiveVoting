<?php
namespace LiveVoting\Context;
/**
 * Class xlvoILIAS
 *
 * @package LiveVoting\Context
 */
class xlvoILIAS
{
    /**
     * @param $key
     * @return mixed
     */
    public function getSetting($key)
    {
        global $ilSetting;

        return $ilSetting->get($key);
    }
}