<?php

require_once('./Services/Component/classes/class.ilPluginConfigGUI.php');
require_once('class.ilLiveVotingConfig.php');
require_once('class.ilLiveVotingPlugin.php');
require_once('class.ilObjLiveVoting.php');

/**
 * Class ilLiveVotingConfigGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilLiveVotingConfigGUI extends ilPluginConfigGUI {

	/**
	 * @var ilLiveVotingConfig
	 */
	protected $object;
	/**q
	 *
	 * @var array
	 */
	protected $fields = array();
	/**
	 * @var string
	 */
	protected $table_name = '';
	/**
	 * @var
	 */
	public $form;


	function __construct() {
		global $ilCtrl, $tpl, $ilTabs;
		/**
		 * @var $ilCtrl ilCtrl
		 * @var $tpl    ilTemplate
		 * @var $ilTabs ilTabsGUI
		 */
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->tabs = &$ilTabs;
		$this->pl = ilLiveVotingPlugin::getInstance();
		$this->object = new ilLiveVotingConfig($this->pl->getConfigTableName());
	}


	public function initTabs() {
		$this->tabs->addTab('edit_settings', $this->pl->txt('edit_settings'), $this->ctrl->getLinkTarget($this, 'configure'));
		// $this->tabs->addTab('htaccess', $this->pl->txt('htaccess'), $this->ctrl->getLinkTarget($this, 'htaccess'));
		if ($this->object->getValue('use_stresstest')) {
			$this->tabs->addTab('stressTest', $this->pl->txt('stress_test'), $this->ctrl->getLinkTarget($this, 'stressTest'));
		}
	}


	/**
	 * @return array
	 */
	public function getFields() {
		$this->fields = array(
			'allow_freeze' => array(
				'type' => 'ilCheckboxInputGUI',
			),
			'allow_fullscreen' => array(
				'type' => 'ilCheckboxInputGUI',
//				'subelements' => array(
//					'jquery' => array(
//						'type' => 'ilCheckboxInputGUI',
//					),
//				)
			),
			'allow_shortlink' => array(
				'type' => 'ilCheckboxInputGUI',
				'info' => "see README_AND_INSTALL",
				'subelements' => array(
					'link' => array(
						'type' => 'ilTextInputGUI',
					),
				)
			),
			'global_anonymous' => array(
				'type' => 'ilCheckboxInputGUI',
			),
			'use_smslog' => array(
				'type' => 'ilCheckboxInputGUI',
			),
			'use_qr' => array(
				'type' => 'ilCheckboxInputGUI',
			),
			'sragsms' => array(
				'type' => 'ilCheckboxInputGUI',
				'subelements' => array(
					'number' => array(
						'type' => 'ilTextInputGUI',
					),
					'keyword' => array(
						'type' => 'ilTextInputGUI',
					),
				)
			),
			'use_responsive' => array(
				'type' => 'ilCheckboxInputGUI',
			),
		);

		return $this->fields;
	}


	/**
	 * @return string
	 */
	public function getTableName() {
		return $this->table_name;
	}


	/**
	 * @return ilLiveVotingConfig
	 */
	public function getObject() {
		return $this->object;
	}


	/**
	 * Handles all commmands, default is 'configure'
	 */
	function performCommand($cmd) {
		$this->initTabs();
		switch ($cmd) {
			case 'configure':
			case 'save':
			case 'htaccess':
			case "stressTest":
			case "performStressTest":
				$this->$cmd();
				break;
		}
	}


	function configure() {
		$this->tabs->setTabActive('edit_settings');
		$this->initConfigurationForm();
		$this->getValues();
		$this->tpl->setContent($this->form->getHTML());
	}


	public function htaccess() {
		$this->tabs->setTabActive('htaccess');
		$this->tpl->setContent('lorem');
	}


	public function stressTest() {
		global $tpl;
		$this->tabs->setTabActive('stressTest');
		$this->initStressTestForm();
		$tpl->setContent($this->form->getHTML());
	}


	public function performStressTest() {
		global $tpl;
		$this->initStressTestForm();
		if ($this->form->checkInput()) {
			require_once('class.ilLiveVotingStressTest.php');
			$st = new ilLiveVotingStressTest($this->form->getInput('pin'), $this->form->getInput('votes'), $this->form->getInput('slow'));
			$st->run();
		}
		$this->tabs->setTabActive('stressTest');
		$tpl->setContent('<dl>
			<dt>Time:</dt>
			<dd>' . $st->getEndTime() - $st->getStartTime() . '</dd>
			</dl>');
	}


	public function initStressTestForm() {
		global $ilCtrl, $ilDB;
		$set = $ilDB->query("SELECT pin FROM rep_robj_xlvo_data LIMIT 0, 2000000");
		while ($rec = $ilDB->fetchObject($set)) {
			$obj = ilObjLiveVoting::_getObjectByPin($rec->pin);
			$pins[$rec->pin] = $rec->pin . ' ' . $obj->getTitle();
		}
		asort($pins);
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();
		$se = new ilSelectInputGUI($this->plugin_object->txt('pin'), 'pin');
		$se->setOptions($pins);
		$this->form->addItem($se);
		$cb = new ilCheckboxInputGUI($this->plugin_object->txt('slow'), 'slow');
		$cb->setValue(1);
		$this->form->addItem($cb);
		$se = new ilSelectInputGUI($this->plugin_object->txt('votes'), 'votes');
		for ($x = 1; $x <= 20; $x ++) {
			$opt[$x * 100] = $x * 100;
		}
		$se->setOptions($opt);
		$this->form->addItem($se);
		$this->form->addCommandButton('performStressTest', $this->plugin_object->txt('perform_stress_test'));
		$this->form->setFormAction($ilCtrl->getFormAction($this));
		require_once('class.ilObjLiveVotingGUI.php');
		ilObjLiveVotingGUI::_addWaitBox('div.ilFormFooter ');
	}


	public function getValues() {
		foreach ($this->getFields() as $key => $item) {
			$values[$key] = $this->object->getValue($key);
			if (is_array($item['subelements'])) {
				foreach ($item['subelements'] as $subkey => $subitem) {
					$values[$key . '_' . $subkey] = $this->object->getValue($key . '_' . $subkey);
				}
			}
		}
		$this->form->setValuesByArray($values);
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	public function initConfigurationForm() {
		global $lng, $ilCtrl;
		include_once('Services/Form/classes/class.ilPropertyFormGUI.php');
		$this->form = new ilPropertyFormGUI();
		foreach ($this->getFields() as $key => $item) {
			$field = new $item['type']($this->pl->txt($key), $key);
			if ($item['info']) {
				$field->setInfo($item['info']);
			}
			if (is_array($item['subelements'])) {
				foreach ($item['subelements'] as $subkey => $subitem) {
					$subfield = new $subitem['type']($this->pl->txt($key . '_' . $subkey), $key . '_' . $subkey);
					if ($subitem['info']) {
						$subfield->setInfo($subitem['info']);
					}
					$field->addSubItem($subfield);
				}
			}
			$this->form->addItem($field);
		}
		$this->form->addCommandButton('save', $lng->txt('save'));
		$this->form->setTitle($this->pl->txt('configuration'));
		$this->form->setFormAction($ilCtrl->getFormAction($this));

		return $this->form;
	}


	public function save() {
		global $tpl, $ilCtrl;
		$this->initConfigurationForm();
		if ($this->form->checkInput()) {
			foreach ($this->getFields() as $key => $item) {
				$this->object->setValue($key, $this->form->getInput($key));
				if (is_array($item['subelements'])) {
					foreach ($item['subelements'] as $subkey => $subitem) {
						$this->object->setValue($key . '_' . $subkey, $this->form->getInput($key . '_' . $subkey));
					}
				}
			}
			ilUtil::sendSuccess($this->pl->txt('conf_saved'), true);
			$ilCtrl->redirect($this, 'configure');
		} else {
			$this->form->setValuesByPost();
			$tpl->setContent($this->form->getHtml());
		}
	}


	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	public static function _getValue($key) {
		$pl = ilLiveVotingPlugin::getInstance();

		return $pl->getConfigObject()->getValue($key);
	}
}

?>
