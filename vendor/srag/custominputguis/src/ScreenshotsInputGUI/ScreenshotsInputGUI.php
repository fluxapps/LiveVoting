<?php

namespace srag\CustomInputGUIs\LiveVoting\ScreenshotsInputGUI;

use GuzzleHttp\Psr7\UploadedFile;
use ilFormException;
use ilFormPropertyGUI;
use ILIAS\FileUpload\DTO\ProcessingStatus;
use ILIAS\FileUpload\DTO\UploadResult;
use ilTemplate;
use srag\DIC\LiveVoting\DICTrait;
use srag\DIC\LiveVoting\Plugin\Pluginable;
use srag\DIC\LiveVoting\Plugin\PluginInterface;
use srag\DIC\LiveVoting\Version\PluginVersionParameter;

/**
 * Class ScreenshotsInputGUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\ScreenshotsInputGUI
 */
class ScreenshotsInputGUI extends ilFormPropertyGUI implements Pluginable
{

    use DICTrait;

    const LANG_MODULE = "screenshotsinputgui";
    /**
     * @var bool
     */
    protected static $init = false;
    /**
     * @var string[]
     */
    protected $allowed_formats = ["bmp", "gif", "jpg", "png"];
    /**
     * @var PluginInterface|null
     */
    protected $plugin = null;
    /**
     * @var UploadResult[]
     */
    protected $screenshots = [];


    /**
     * ScreenshotsInputGUI constructor
     *
     * @param string $title
     * @param string $post_var
     */
    public function __construct(string $title = "", string $post_var = "")
    {
        parent::__construct($title, $post_var);
    }


    /**
     * @inheritDoc
     */
    public function checkInput() : bool
    {
        $this->processScreenshots();

        if ($this->getRequired() && empty($this->getValue())) {
            $this->setAlert(self::dic()->language()->txt("msg_input_is_required"));

            return false;
        }

        return true;
    }


    /**
     * @return string[]
     */
    public function getAllowedFormats() : array
    {
        return $this->allowed_formats;
    }


    /**
     * @param string[] $allowed_formats
     *
     * @return self
     */
    public function setAllowedFormats(array $allowed_formats) : self
    {
        $this->allowed_formats = $allowed_formats;

        return $this;
    }


    /**
     * @return string
     */
    public function getJSOnLoadCode() : string
    {
        $screenshot_tpl = $this->getPlugin()->template(__DIR__ . "/templates/screenshot.html", true, true, false);
        $screenshot_tpl->setVariableEscaped("TXT_REMOVE_SCREENSHOT", $this->getPlugin()
            ->translate("remove_screenshot", self::LANG_MODULE));
        $screenshot_tpl->setVariableEscaped("TXT_PREVIEW_SCREENSHOT", $this->getPlugin()
            ->translate("preview_screenshot", self::LANG_MODULE));

        return 'il.ScreenshotsInputGUI.PAGE_SCREENSHOT_NAME = ' . json_encode($this->getPlugin()
                ->translate("page_screenshot", self::LANG_MODULE)) . ';
		il.ScreenshotsInputGUI.SCREENSHOT_TEMPLATE = ' . json_encode(self::output()->getHTML($screenshot_tpl)) . ';';
    }


    /**
     * @inheritDoc
     */
    public function getPlugin() : PluginInterface
    {
        return $this->plugin;
    }


    /**
     * @return UploadResult[]
     */
    public function getValue() : array
    {
        $this->processScreenshots();

        return $this->screenshots;
    }


    /**
     *
     */
    public function init()/*: void*/
    {
        if (self::$init === false) {
            self::$init = true;

            $version_parameter = PluginVersionParameter::getInstance()->withPlugin($this->getPlugin());

            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

            self::dic()->ui()->mainTemplate()->addJavaScript($version_parameter->appendToUrl($dir . "/../../node_modules/es6-promise/dist/es6-promise.auto.min.js"));
            self::dic()->ui()->mainTemplate()->addJavaScript($version_parameter->appendToUrl($dir . "/../../node_modules/canvas-toBlob/canvas-toBlob.js"));
            self::dic()->ui()->mainTemplate()->addJavaScript($version_parameter->appendToUrl($dir . "/../../node_modules/html2canvas/dist/html2canvas.min.js"));

            self::dic()->ui()->mainTemplate()->addJavaScript($version_parameter->appendToUrl($dir . "/js/ScreenshotsInputGUI.min.js", $dir . "/js/ScreenshotsInputGUI.js"), false);
            self::dic()->ui()->mainTemplate()->addOnLoadCode($this->getJSOnLoadCode());
        }
    }


    /**
     * @param ilTemplate $tpl
     */
    public function insert(ilTemplate $tpl)/*: void*/
    {
        $html = $this->render();

        $tpl->setCurrentBlock("prop_generic");
        $tpl->setVariable("PROP_GENERIC", $html);
        $tpl->parseCurrentBlock();
    }


    /**
     * @return string
     */
    public function render() : string
    {
        $this->init();

        $screenshots_tpl = $this->getPlugin()->template(__DIR__ . "/templates/screenshots.html", true, true, false);
        $screenshots_tpl->setVariable("TXT_UPLOAD_SCREENSHOT", $this->getPlugin()
            ->translate("upload_screenshot", self::LANG_MODULE));
        $screenshots_tpl->setVariable("TXT_TAKE_PAGE_SCREENSHOT", $this->getPlugin()
            ->translate("take_page_screenshot", self::LANG_MODULE));
        $screenshots_tpl->setVariable("POST_VAR", $this->getPostVar());
        $screenshots_tpl->setVariable("ALLOWED_FORMATS", implode(",", array_map(function (string $format) : string {
            return "." . $format;
        }, $this->getAllowedFormats())));

        return self::output()->getHTML($screenshots_tpl);
    }


    /**
     * @param string $post_var
     *
     * @return self
     */
    public function setPostVar(/*string*/ $post_var) : self
    {
        $this->postvar = $post_var;

        return $this;
    }


    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle(/*string*/ $title) : self
    {
        $this->title = $title;

        return $this;
    }


    /**
     * @param UploadResult[] $screenshots
     *
     * @throws ilFormException
     */
    public function setValue(/*array*/ $screenshots)/*: void*/
    {
        //throw new ilFormException("ScreenshotsInputGUI does not support set screenshots!");
    }


    /**
     * @param array $values
     *
     * @throws ilFormException
     */
    public function setValueByArray(/*array*/ $values)/*: void*/
    {
        //throw new ilFormException("ScreenshotsInputGUI does not support set screenshots!");
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
     *
     */
    protected function processScreenshots()/*: void*/
    {
        $this->screenshots = [];

        if (!self::dic()->upload()->hasBeenProcessed()) {
            self::dic()->upload()->process();
        }

        if (self::dic()->upload()->hasUploads()) {
            $uploads = self::dic()->http()->request()->getUploadedFiles()[$this->getPostVar()];

            if (is_array($uploads)) {
                $uploads = array_values(array_flip(array_map(function (UploadedFile $file) : string {
                    return $file->getClientFilename();
                }, $uploads)));

                $this->screenshots = array_values(array_filter(self::dic()->upload()
                    ->getResults(), function (UploadResult $file) use (&$uploads) : bool {
                    $ext = pathinfo($file->getName(), PATHINFO_EXTENSION);

                    return ($file->getStatus()->getCode() === ProcessingStatus::OK && in_array($file->getPath(), $uploads)
                        && in_array($ext, $this->allowed_formats));
                }));
            }
        }
    }
}
