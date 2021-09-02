<?php

namespace srag\DIC\LiveVoting\Output;

use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\Template;
use ilTable2GUI;
use ilTemplate;
use JsonSerializable;
use srag\DIC\LiveVoting\DICTrait;
use srag\DIC\LiveVoting\Exception\DICException;
use stdClass;

/**
 * Class Output
 *
 * @package srag\DIC\LiveVoting\Output
 */
final class Output implements OutputInterface
{

    use DICTrait;

    /**
     * Output constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function getHTML($value) : string
    {
        if (is_array($value)) {
            $html = "";
            foreach ($value as $gui) {
                $html .= $this->getHTML($gui);
            }
        } else {
            switch (true) {
                // HTML
                case (is_string($value)):
                    $html = $value;
                    break;

                // Component instance
                case ($value instanceof Component):
                    if (self::dic()->ctrl()->isAsynch()) {
                        $html = self::dic()->ui()->renderer()->renderAsync($value);
                    } else {
                        $html = self::dic()->ui()->renderer()->render($value);
                    }
                    break;

                // ilTable2GUI instance
                case ($value instanceof ilTable2GUI):
                    // Fix stupid broken ilTable2GUI (render has only header without rows)
                    $html = $value->getHTML();
                    break;

                // GUI instance
                case method_exists($value, "render"):
                    $html = $value->render();
                    break;
                case method_exists($value, "getHTML"):
                    $html = $value->getHTML();
                    break;

                // Template instance
                case ($value instanceof ilTemplate):
                case ($value instanceof Template):
                    $html = $value->get();
                    break;

                // Not supported!
                default:
                    throw new DICException("Class " . get_class($value) . " is not supported for output!", DICException::CODE_OUTPUT_INVALID_VALUE);
                    break;
            }
        }

        return strval($html);
    }


    /**
     * @inheritDoc
     */
    public function output($value, bool $show = false, bool $main_template = true) : void
    {
        $html = $this->getHTML($value);

        if (self::dic()->ctrl()->isAsynch()) {
            echo $html;

            exit;
        } else {
            if ($main_template) {
                self::dic()->ui()->mainTemplate()->loadStandardTemplate();
            }

            self::dic()->ui()->mainTemplate()->setLocator();

            if (!empty($html)) {
                self::dic()->ui()->mainTemplate()->setContent($html);
            }

            if ($show) {
                self::dic()->ui()->mainTemplate()->printToStdout();
            }
        }
    }


    /**
     * @inheritDoc
     */
    public function outputJSON($value) : void
    {
        switch (true) {
            case (is_string($value)):
            case (is_int($value)):
            case (is_double($value)):
            case (is_bool($value)):
            case (is_array($value)):
            case ($value instanceof stdClass):
            case ($value === null):
            case ($value instanceof JsonSerializable):
                $value = json_encode($value);

                header("Content-Type: application/json; charset=utf-8");

                echo $value;

                exit;

                break;

            default:
                throw new DICException(get_class($value) . " is not a valid JSON value!", DICException::CODE_OUTPUT_INVALID_VALUE);
                break;
        }
    }
}
