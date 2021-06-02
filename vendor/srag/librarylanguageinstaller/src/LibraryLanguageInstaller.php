<?php

namespace srag\LibraryLanguageInstaller\LiveVoting;

use ilDBConstants;
use ilGlobalCache;
use ilObjLanguage;
use srag\DIC\LiveVoting\DICTrait;
use srag\DIC\LiveVoting\Plugin\Pluginable;
use srag\DIC\LiveVoting\Plugin\PluginInterface;

/**
 * Class LibraryLanguageInstaller
 *
 * @package srag\LibraryLanguageInstaller\LiveVoting
 */
final class LibraryLanguageInstaller implements Pluginable
{

    use DICTrait;

    /**
     * @var string
     */
    protected $library_language_directory = "";
    /**
     * @var PluginInterface|null
     */
    protected $plugin = null;


    /**
     * LibraryLanguageInstaller constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        return new self();
    }


    /**
     * @inheritDoc
     */
    public function getPlugin() : PluginInterface
    {
        return $this->plugin;
    }


    /**
     * https://github.com/ILIAS-eLearning/ILIAS/blob/da3a38a7c9f2ae0169cb88b485f986deb1c24aaf/Services/Component/classes/class.ilPlugin.php#L409
     */
    public function updateLanguages()/*: void*/
    {
        ilGlobalCache::flushAll();

        // get the keys of all installed languages if keys are not provided
        $a_lang_keys = [];
        foreach (ilObjLanguage::getInstalledLanguages() as $langObj) {
            if ($langObj->isInstalled()) {
                $a_lang_keys[] = $langObj->getKey();
            }
        }

        $langs = $this->getAvailableLangFiles();

        $prefix = $this->getPlugin()->getPluginObject()->getPrefix();

        foreach ($langs as $lang) {
            // check if the language should be updated, otherwise skip it
            if (!in_array($lang["key"], $a_lang_keys)) {
                continue;
            }

            $txt = file($this->library_language_directory . "/" . $lang["file"]);

            // Read already plugin language keys for not delete them
            $lang_array = unserialize(self::dic()->database()->queryF("SELECT lang_array FROM lng_modules WHERE lang_key = %s AND module = %s", [
                ilDBConstants::T_TEXT,
                ilDBConstants::T_TEXT
            ], [$lang["key"], $prefix])->fetchAssoc()["lang_array"]);
            if (!is_array($lang_array)) {
                $lang_array = [];
            }

            // get locally changed variables of the module (these should be kept)
            $local_changes = ilObjLanguage::_getLocalChangesByModule($lang["key"], $prefix);

            // get language data
            if (is_array($txt)) {
                foreach ($txt as $row) {
                    if ($row[0] != "#" && strpos($row, "#:#") > 0) {
                        $a = explode("#:#", trim($row));
                        $identifier = $prefix . "_" . trim($a[0]);
                        $value = trim($a[1]);

                        if (!isset($lang_array[$identifier])) { // Allow plugins to modify library languages if needed
                            if (isset($local_changes[$identifier])) {
                                $lang_array[$identifier] = $local_changes[$identifier];
                            } else {
                                $lang_array[$identifier] = $value;
                                ilObjLanguage::replaceLangEntry($prefix, $identifier, $lang["key"], $value);
                            }
                        }
                    }
                }
            }

            ilObjLanguage::replaceLangModule($lang["key"], $prefix, $lang_array);
        }
    }


    /**
     * @param string $library_language_directory
     *
     * @return self
     */
    public function withLibraryLanguageDirectory(string $library_language_directory) : self
    {
        $this->library_language_directory = $library_language_directory;

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function withPlugin(PluginInterface $plugin) : self
    {
        $this->plugin = $plugin;

        return $this;
    }


    /**
     * https://github.com/ILIAS-eLearning/ILIAS/blob/da3a38a7c9f2ae0169cb88b485f986deb1c24aaf/Services/Component/classes/class.ilPlugin.php#L310
     *
     * @return array
     */
    protected function getAvailableLangFiles() : array
    {
        $langs = [];

        if (!@is_dir($this->library_language_directory)) {
            return [];
        }

        $dir = opendir($this->library_language_directory);
        while ($file = readdir($dir)) {
            if ($file !== "." && $file !== "..") {
                // directories
                if (@is_file($this->library_language_directory . "/" . $file)) {
                    if (substr($file, 0, 6) === "ilias_"
                        && substr($file, strlen($file) - 5) === ".lang"
                    ) {
                        $langs[] = [
                            "key"  => substr($file, 6, 2),
                            "file" => $file,
                            "path" => $this->library_language_directory . "/" . $file
                        ];
                    }
                }
            }
        }

        return $langs;
    }
}
