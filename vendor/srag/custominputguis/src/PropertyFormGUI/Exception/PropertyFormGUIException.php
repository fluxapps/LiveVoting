<?php

namespace srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Exception;

use ilFormException;

/**
 * Class PropertyFormGUIException
 *
 * @package srag\CustomInputGUIs\LiveVoting\PropertyFormGUI\Exception
 *
 * @deprecated
 */
final class PropertyFormGUIException extends ilFormException
{

    /**
     * @var int
     *
     * @deprecated
     */
    const CODE_INVALID_FIELD = 2;
    /**
     * @var int
     *
     * @deprecated
     */
    const CODE_INVALID_PROPERTY_CLASS = 1;
    /**
     * @var int
     *
     * @deprecated
     */
    const CODE_MISSING_CONST_CONFIG_CLASS_NAME = 3;


    /**
     * PropertyFormGUIException constructor
     *
     * @param string $message
     * @param int    $code
     *
     * @deprecated
     */
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
