<?php

$jsonMenu = new jsonMenu();
$jsonMenu->draw();


/**
 * Class jsonMenu
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class jsonMenu {

	/**
	 * @var int
	 */
	protected $menu_id = 0;


	public function __construct() {
		self::initILIAS();
		$this->setMenuId($_REQUEST['menu_id']);
		$this->initHeader();
	}


	public function initHeader() {
		// header('Content-type: application/json');
	}


	public function draw() {
		$menu_gui = new ctrlmmMenuGUI($this->getMenuId());
		echo $menu_gui->getHTML();
	}


	/**
	 * @param int $menu_id
	 */
	public function setMenuId($menu_id) {
		$this->menu_id = $menu_id;
	}


	/**
	 * @return int
	 */
	public function getMenuId() {
		return $this->menu_id;
	}


	private static function initILIAS() {
		chdir(stristr(__FILE__, 'Customizing', true));
		require_once('include/inc.header.php');
		self::includes();
	}


	private static function includes() {
		require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/Menu/class.ctrlmmMenuGUI.php');
	}
}

?>
