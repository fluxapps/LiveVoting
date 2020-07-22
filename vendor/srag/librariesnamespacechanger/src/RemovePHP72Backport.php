<?php

namespace srag\LibrariesNamespaceChanger;

use Composer\Script\Event;

/**
 * Class RemovePHP72Backport
 *
 * @package srag\LibrariesNamespaceChanger
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @internal
 */
final class RemovePHP72Backport
{

    /**
     * @var self|null
     */
    private static $instance = null;
    /**
     * @var array
     */
    private static $exts
        = [
            "md",
            "php"
        ];


    /**
     * @param Event $event
     *
     * @return self
     */
    private static function getInstance(Event $event) : self
    {
        if (self::$instance === null) {
            self::$instance = new self($event);
        }

        return self::$instance;
    }


    /**
     * @param Event $event
     *
     * @internal
     */
    public static function RemovePHP72Backport(Event $event)/*: void*/
    {
        self::getInstance($event)->doRemovePHP72Backport();
    }


    /**
     * @var Event
     */
    private $event;


    /**
     * RemovePHP72Backport constructor
     *
     * @param Event $event
     */
    private function __construct(Event $event)
    {
        $this->event = $event;
    }


    /**
     *
     */
    private function doRemovePHP72Backport()/*: void*/
    {
        $files = [];

        $this->getFiles(__DIR__ . "/../../../..", $files);

        foreach ($files as $file) {
            $code = file_get_contents($file);

            $code = $this->removeConvertPHP72To70($code);

            file_put_contents($file, $code);
        }
    }


    /**
     * @param string $code
     *
     * @return string
     */
    protected function removeConvertPHP72To70(string $code) : string
    {
        // Run for each found function
        $new_code = preg_replace_callback("/(" . PHP72Backport::REGEXP_FUNCTION . ")/", function (array $matches) : string {
            $function = $matches[0];

            // : void
            $function = preg_replace("/(\)\s*)\/\*(\s*:\s*void\s*)\*\//", '$1$2', $function);

            // : object
            $function = preg_replace("/(\)\s*)\/\*(\s*:\s*object\s*)\*\//", '$1$2', $function);

            // : ?type
            $function = preg_replace("/(\)\s*)\/\*(\s*:\s*\?\s*" . PHP72Backport::REGEXP_NAME . "\s*)\*\//", '$1$2', $function);

            // object $param
            $function = preg_replace("/([(,]\s*)\/\*(\s*object\s*)\*\/(\s*\\$" . PHP72Backport::REGEXP_NAME . ")/", '$1$2$3', $function);

            // ?type $param
            $function = preg_replace("/([(,]\s*)\/\*(\s*\?\s*" . PHP72Backport::REGEXP_NAME . "\s*)\*\/(\s*\\$" . PHP72Backport::REGEXP_NAME . ")/", '$1$2$3', $function);

            return $function;
        }, $code);

        if (is_string($new_code)) {
            return $new_code;
        } else {
            // TODO: PREG_BACKTRACK_LIMIT_ERROR on PHP 7.0 code?
            return $code;
        }
    }


    /**
     * @param string $folder
     * @param array  $files
     */
    private function getFiles(string $folder, array &$files = [])/*: void*/
    {
        $paths = scandir($folder);

        foreach ($paths as $file) {
            if ($file !== "." && $file !== "..") {
                $path = $folder . "/" . $file;

                if (is_dir($path)) {
                    $this->getFiles($path, $files);
                } else {
                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    if (in_array($ext, self::$exts)) {
                        array_push($files, $path);
                    }
                }
            }
        }
    }
}
