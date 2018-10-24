<?php

namespace srag\CustomInputGUIs\ScreenshotsInputGUI;

use GuzzleHttp\Psr7\UploadedFile;
use ilFormException;
use ilFormPropertyGUI;
use ILIAS\FileUpload\DTO\ProcessingStatus;
use ILIAS\FileUpload\DTO\UploadResult;
use ilTemplate;
use srag\DIC\DICTrait;
use srag\DIC\Plugin\Plugin;
use srag\DIC\Plugin\Pluginable;
use srag\DIC\Plugin\PluginInterface;

/**
 * Class ScreenshotsInputGUI
 *
 * @package srag\CustomInputGUIs\ScreenshotsInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @since   ILIAS 5.3
 */
class ScreenshotsInputGUI extends ilFormPropertyGUI implements Pluginable {

	use DICTrait;
	const LANG_MODULE_SCREENSHOTSINPUTGUI = "screenshotsinputgui";
	/**
	 * @var bool
	 */
	protected static $init = false;
	/**
	 * @var string[]
	 */
	protected $allowed_formats = [ "bmp", "gif", "jpg", "png" ];
	/**
	 * @var UploadResult[]
	 */
	protected $screenshots = [];
	/**
	 * @var Plugin|null
	 */
	protected $plugin = NULL;


	/**
	 * ScreenshotsInputGUI constructor
	 *
	 * @param string $title
	 * @param string $post_var
	 */
	public function __construct(string $title = "", string $post_var = "") {
		parent::__construct($title, $post_var);
	}


	/**
	 * @return bool
	 */
	public function checkInput(): bool {
		$this->processScreenshots();

		if ($this->getRequired() && count($this->screenshots) === 0) {
			return false;
		}

		return true;
	}


	/**
	 * @return string[]
	 */
	public function getAllowedFormats(): array {
		return $this->allowed_formats;
	}


	/**
	 * @return string
	 */
	public function getJSOnLoadCode(): string {
		$screenshot_tpl = $this->getPlugin()->template(__DIR__ . "/templates/screenshot.html", true, true, false);
		$screenshot_tpl->setVariable("TXT_REMOVE_SCREENSHOT", $this->getPlugin()
			->translate("remove_screenshot", self::LANG_MODULE_SCREENSHOTSINPUTGUI));
		$screenshot_tpl->setVariable("TXT_PREVIEW_SCREENSHOT", $this->getPlugin()
			->translate("preview_screenshot", self::LANG_MODULE_SCREENSHOTSINPUTGUI));

		return 'il.ScreenshotsInputGUI.PAGE_SCREENSHOT_NAME = ' . json_encode($this->getPlugin()
				->translate("page_screenshot", self::LANG_MODULE_SCREENSHOTSINPUTGUI)) . ';
		il.ScreenshotsInputGUI.SCREENSHOT_TEMPLATE = ' . json_encode($screenshot_tpl->get()) . ';';
	}


	/**
	 * @return PluginInterface
	 */
	public function getPlugin(): PluginInterface {
		return $this->plugin;
	}


	/**
	 * @return UploadResult[]
	 */
	public function getValue(): array {
		return $this->screenshots;
	}


	/**
	 *
	 */
	public function initJS()/*: void*/ {
		if (self::$init === false) {
			self::$init = true;

			$dir = substr(__DIR__, strlen(ILIAS_ABSOLUTE_PATH) + 1);

			self::dic()->mainTemplate()->addJavaScript($dir . "/../../node_modules/es6-promise/dist/es6-promise.auto.min.js");
			self::dic()->mainTemplate()->addJavaScript($dir . "/../../node_modules/canvas-toBlob/canvas-toBlob.js");
			self::dic()->mainTemplate()->addJavaScript($dir . "/../../node_modules/html2canvas/dist/html2canvas.min.js");

			self::dic()->mainTemplate()->addJavaScript($dir . "/js/ScreenshotsInputGUI.min.js", false);
			self::dic()->mainTemplate()->addOnLoadCode($this->getJSOnLoadCode());
		}
	}


	/**
	 * @param ilTemplate $tpl
	 */
	public function insert(ilTemplate $tpl) /*: void*/ {
		$html = $this->render();

		$tpl->setCurrentBlock("prop_generic");
		$tpl->setVariable("PROP_GENERIC", $html);
		$tpl->parseCurrentBlock();
	}


	/**
	 *
	 */
	protected function processScreenshots()/*: void*/ {
		$this->screenshots = [];

		if (!self::dic()->upload()->hasBeenProcessed()) {
			self::dic()->upload()->process();
		}

		if (self::dic()->upload()->hasUploads()) {
			$uploads = self::dic()->http()->request()->getUploadedFiles()[$this->getPostVar()];

			if (is_array($uploads)) {
				$uploads = array_values(array_flip(array_map(function (UploadedFile $file): string {
					return $file->getClientFilename();
				}, $uploads)));

				$this->screenshots = array_values(array_filter(self::dic()->upload()
					->getResults(), function (UploadResult $file) use (&$uploads): bool {
					$ext = pathinfo($file->getName(), PATHINFO_EXTENSION);

					return ($file->getStatus()->getCode() === ProcessingStatus::OK && in_array($file->getPath(), $uploads)
						&& in_array($ext, $this->allowed_formats));
				}));
			}
		}
	}


	/**
	 * @return string
	 */
	protected function render(): string {
		$this->initJS();

		$screenshots_tpl = $this->getPlugin()->template(__DIR__ . "/templates/screenshots.html", true, true, false);
		$screenshots_tpl->setVariable("TXT_UPLOAD_SCREENSHOT", $this->getPlugin()
			->translate("upload_screenshot", self::LANG_MODULE_SCREENSHOTSINPUTGUI));
		$screenshots_tpl->setVariable("TXT_TAKE_PAGE_SCREENSHOT", $this->getPlugin()
			->translate("take_page_screenshot", self::LANG_MODULE_SCREENSHOTSINPUTGUI));
		$screenshots_tpl->setVariable("POST_VAR", $this->getPostVar());
		$screenshots_tpl->setVariable("ALLOWED_FORMATS", implode(",", array_map(function (string $format): string {
			return "." . $format;
		}, $this->allowed_formats)));

		return $screenshots_tpl->get();
	}


	/**
	 * @param string[] $allowed_formats
	 */
	public function setAllowedFormats(array $allowed_formats)/*: void*/ {
		$this->allowed_formats = $allowed_formats;
	}


	/**
	 * @param PluginInterface $plugin
	 */
	public function setPlugin(PluginInterface $plugin)/*: void*/ {
		$this->plugin = $plugin;
	}


	/**
	 * @param UploadResult[] $screenshots
	 *
	 * @throws ilFormException
	 */
	public function setValue(array $screenshots)/*: void*/ {
		//throw new ilFormException("ScreenshotInputGUI does not support set screenshots!");
	}


	/**
	 * @param array $values
	 *
	 * @throws ilFormException
	 */
	public function setValueByArray($values)/*: void*/ {
		//throw new ilFormException("ScreenshotInputGUI does not support set screenshots!");
	}
}
