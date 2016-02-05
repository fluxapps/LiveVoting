<?php

/**
 * Class xlvoJs
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoJs {

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
	 * xlvoJs constructor.
	 * @param $name
	 * @param string $category
	 */
	public function __construct($name, $category = '') {
		$this->setName($name);
		$this->setCategory($category);
		$this->resolveLib();
	}


	/**
	 * @param $name
	 * @param $base_url
	 * @param string $category
	 * @return xlvoJs
	 */
	public static function fastInit($name, $base_url, $category = '') {
		$obj = new self($name, $category);
		$obj->init(xlvoJsSettings::getInstance($base_url));

		return $obj;
	}


	public function resolveLib() {
		$base_path = './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/js/';
		$category = ($this->getCategory() ? $this->getCategory() . '/' : '') . $this->getName() . '/';
		$file_name = 'xlvo' . $this->getName() . '.js';
		$this->setLib($base_path . $category . $file_name);
	}


	/**
	 * @param xlvoJsSettings $xlvoJsSettings
	 */
	public function init(xlvoJsSettings $xlvoJsSettings) {
		global $tpl;
		$tpl->addJavaScript($this->getLib());
		$tpl->addOnLoadCode('xlvo' . $this->getName() . '.init(\'' . $xlvoJsSettings->asJson() . '\');');
	}


	/**
	 * @param $method
	 * @param string $params
	 */
	public function call($method, $params = '') {
		global $tpl;
		$tpl->addOnLoadCode('xlvo' . $this->getName() . '.' . $method . '(' . $params . ');');
	}


	/**
	 * @return string
	 */
	public function getClassName() {
		return $this->class_name;
	}


	/**
	 * @param string $class_name
	 */
	public function setClassName($class_name) {
		$this->class_name = $class_name;
	}


	/**
	 * @return string
	 */
	public function getSettingClassName() {
		return $this->setting_class_name;
	}


	/**
	 * @param string $setting_class_name
	 */
	public function setSettingClassName($setting_class_name) {
		$this->setting_class_name = $setting_class_name;
	}


	/**
	 * @return boolean
	 */
	public function isInit() {
		return $this->init;
	}


	/**
	 * @param boolean $init
	 */
	public function setInit($init) {
		$this->init = $init;
	}


	/**
	 * @return string
	 */
	public function getLib() {
		return $this->lib;
	}


	/**
	 * @param string $lib
	 */
	public function setLib($lib) {
		$this->lib = $lib;
	}


	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * @return string
	 */
	public function getCategory() {
		return $this->category;
	}


	/**
	 * @param string $category
	 */
	public function setCategory($category) {
		$this->category = $category;
	}
}

/**
 * Class xlvoJsSettings
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoJsSettings {

	/**
	 * @var array
	 */
	protected $settings = array();
	/**
	 * @var array
	 */
	protected $translations = array();


	/**
	 * xlvoJsSettings constructor.
	 * @param string $base_url
	 */
	protected function __construct($base_url) {
		$this->settings['base_url'] = $base_url;
	}


	/**
	 * @param $base_url
	 * @return xlvoJsSettings
	 */
	public static function getInstance($base_url) {
		return new self($base_url);
	}


	/**
	 * @param $name
	 * @param $value
	 */
	public function addSetting($name, $value) {
		$this->settings[$name] = $value;
	}


	/**
	 * @param $key
	 */
	public function addTranslation($key) {
		$pl = ilLiveVotingPlugin::getInstance();
		$this->translations[$key] = $pl->txt($key);
	}


	/**
	 * @return string
	 */
	public function asJson() {
		$arr = array();
		foreach ($this->settings as $name => $value) {
			$arr[$name] = $value;
		}

		foreach ($this->translations as $key => $string) {
			$arr['lng'][$key] = $string;
		}

		return json_encode($arr);
	}
}