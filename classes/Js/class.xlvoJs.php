<?php

namespace LiveVoting\Js;
use LiveVoting\Conf\xlvoConf;
use xlvoGUI;

/**
 * Class xlvoJs
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoJs {

	const DEVELOP = false;
	const API_URL = xlvoConf::API_URL;
	const BASE_URL_SETTING = 'base_url';
	const BASE_PATH = './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/js/';
	/**
	 * @var string
	 */
	protected $class_name = '';
	/**
	 * @var string
	 */
	protected $setting_class_name = '';
	/**
	 * @var bool
	 */
	protected $init = false;
	/**
	 * @var string
	 */
	protected $lib = '';
	/**
	 * @var string
	 */
	protected $name = '';
	/**
	 * @var string
	 */
	protected $category = '';
	/**
	 * @var xlvoJsSettings
	 */
	protected $settings;
	/**
	 * @var \ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var \ilTemplate
	 */
	protected $tpl;


	/**
	 * xlvoJs constructor.
	 */
	protected function __construct() {
		global $DIC;
		$this->settings = new xlvoJsSettings();
		$this->ctrl = $DIC->ctrl();
		$this->tpl = $DIC->ui()->mainTemplate();
	}


	public static function getInstance() {
		return new self();
	}


	/**
	 * @param array $settings
	 * @return $this
	 */
	public function addSettings(array $settings) {
		foreach ($settings as $k => $v) {
			$this->settings->addSetting($k, $v);
		}

		return $this;
	}


	/**
	 * @param array $translations
	 * @return $this
	 */
	public function addTranslations(array $translations) {
		foreach ($translations as $translation) {
			$this->settings->addTranslation($translation);
		}

		return $this;
	}


	/**
	 * @param xlvoGUI $xlvoGUI
	 * @param array $additional_classes
	 * @param string $cmd
	 * @return $this
	 */
	public function api(xlvoGUI $xlvoGUI, array $additional_classes = array(), $cmd = '') {
		$ilCtrl2 = clone($this->ctrl);
		$this->ctrl->initBaseClass(\ilUIPluginRouterGUI::class);
		$ilCtrl2->setTargetScript(self::API_URL);
		$additional_classes[] = get_class($xlvoGUI);

		$this->settings->addSetting(self::BASE_URL_SETTING, $this->ctrl->getLinkTargetByClass($additional_classes, $cmd, null, true));

		return $this;
	}


	/**
	 * @param $name
	 * @return $this
	 */
	public function name($name) {
		$this->name = $name;

		return $this;
	}


	/**
	 * @param $category
	 * @return $this
	 */
	public function category($category) {
		$this->category = $category;

		return $this;
	}


	/**
	 * @param xlvoGUI $xlvoGUI
	 * @param string $cmd
	 * @return $this
	 */
	public function ilias($xlvoGUI, $cmd = '') {
		$this->settings->addSetting(self::BASE_URL_SETTING, $this->ctrl->getLinkTarget($xlvoGUI, $cmd, '', true));

		return $this;
	}


	protected function resolveLib() {
		$base_path = self::BASE_PATH;
		$category = ($this->category ? $this->category . '/' : '') . $this->name . '/';
		$file_name = 'xlvo' . $this->name . '.js';
		$file_name_min = 'xlvo' . $this->name . '.min.js';
		$full_path_min = $base_path . $category . $file_name_min;
		$full_path = $base_path . $category . $file_name;
		if (is_file($full_path_min) && !self::DEVELOP) {
			$this->lib = $full_path_min;
		} else {
			$this->lib = $full_path;
		}
	}


	/**
	 * @return string
	 */
	public function getLibraryURL() {
		$this->resolveLib();

		return $this->lib;
	}


	/**
	 * @return $this
	 */
	public function init() {
		$this->init = true;
		$this->resolveLib();
		$this->addLibToHeader($this->lib, false);
		$this->addOnLoadCode($this->getInitCode());

		return $this;
	}


	/**
	 * @param $code
	 * @return $this
	 */
	public function addOnLoadCode($code) {
		$this->tpl->addOnLoadCode($code);

		return $this;
	}


	/**
	 * @param $method
	 * @param string $params
	 * @return $this
	 */
	public function call($method, $params = '') {
		if (!$this->init) {
			return $this;
		}
		$this->tpl->addOnLoadCode($this->getCallCode($method, $params));

		return $this;
	}


	/**
	 * @return string
	 */
	public function getInitCode() {
		return 'xlvo' . $this->name . '.init(\'' . $this->settings->asJson() . '\');';
	}


	/**
	 * @param $method
	 * @param $params
	 * @return string
	 */
	public function getCallCode($method, $params = '') {
		return 'xlvo' . $this->name . '.' . $method . '(' . $params . ');';
	}


	/**
	 * @param $name_of_lib
	 * @param bool $external
	 * @return $this
	 */
	public function addLibToHeader($name_of_lib, $external = true) {
		if ($external) {
			$this->tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/js/libs/' . $name_of_lib);
		} else {
			$this->tpl->addJavaScript($name_of_lib);
		}

		return $this;
	}
}
