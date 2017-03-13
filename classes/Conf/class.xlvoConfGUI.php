<?php
use LiveVoting\Conf\xlvoConf;

/**
 * Class xlvoConfGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy xlvoConfGUI : xlvoMainGUI
 */
class xlvoConfGUI extends xlvoGUI {

	/**
	 * @param $key
	 *
	 * @return string
	 */
	public function txt($key) {
		return $this->pl->txt('config_' . $key);
	}


	public function index() {
		global $ilToolbar;
		/**
		 * @var $ilToolbar ilToolbarGUI
		 */
		if (xlvoConf::getConfig(xlvoConf::F_RESULT_API)) {
			$b = ilLinkButton::getInstance();
			$b->setUrl($this->ctrl->getLinkTarget($this, 'resetToken'));
			$b->setCaption($this->txt('regenerate_token'), false);
			$ilToolbar->addButtonInstance($b);
		}

		$xlvoConfFormGUI = new xlvoConfFormGUI($this);
		$xlvoConfFormGUI->fillForm();
		$this->tpl->setContent($xlvoConfFormGUI->getHTML());
	}


	protected function resetToken() {
		xlvoConf::set(xlvoConf::F_API_TOKEN, null);
		xlvoConf::getConfig(xlvoConf::F_API_TOKEN);
		$this->cancel();
	}


	protected function update() {
		$xlvoConfFormGUI = new xlvoConfFormGUI($this);
		$xlvoConfFormGUI->setValuesByPost();
		if ($xlvoConfFormGUI->saveObject()) {
			\ilUtil::sendSuccess($this->txt('msg_success'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		}
		$this->tpl->setContent($xlvoConfFormGUI->getHTML());
	}


	protected function confirmDelete() {
		// TODO: Implement confirmDelete() method.
	}


	protected function delete() {
		// TODO: Implement delete() method.
	}


	protected function add() {
		// TODO: Implement add() method.
	}


	protected function create() {
		// TODO: Implement create() method.
	}


	protected function edit() {
		// TODO: Implement edit() method.
	}
}