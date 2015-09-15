<?php

require_once('./Services/Table/classes/class.ilTable2GUI.php');
require_once('./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php');
require_once('./Services/Form/classes/class.ilMultiSelectInputGUI.php');

/**
 * Class xlvoVotingTableGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class xlvoVotingTableGUI extends ilTable2GUI {

	const TBL_ID = 'tbl_xlvo';
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;
	/**
	 * @var xlvoVotingGUI
	 */
	protected $voting_gui;
	/**
	 * @var array
	 */
	protected $filter = array();
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;


	public function  __construct(xlvoVotingGUI $a_parent_obj, $a_parent_cmd) {
		/**
		 * @var $ilCtrl    ilCtrl
		 * @var $ilToolbar ilToolbarGUI
		 */
		global $ilCtrl, $ilToolbar;
		$this->voting_gui = $a_parent_obj;
		$this->toolbar = $ilToolbar;
		$this->ctrl = $ilCtrl;
		$this->pl = ilLiveVotingPlugin::getInstance();

		$this->voting_gui->tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/default/voting/sortable.js');
		//		$this->toolbar->setFormAction($this->ctrl->getFormAction($this->voting_gui));
		//		$b = ilLinkButton::getInstance();
		//		$b->setCaption('rep_robj_xlvo_save_sorting');
		//		$b->setUrl($this->ctrl->getLinkTarget(new xlvoVotingGUI(), 'saveSorting'));
		//		$this->addCommandButtonInstance($b);
		//		$this->toolbar->addFormButton('save sorting', 'saveSorting');

		$this->setId(self::TBL_ID);
		$this->setPrefix(self::TBL_ID);
		$this->setFormName(self::TBL_ID);
		$this->ctrl->saveParameter($a_parent_obj, $this->getNavParameter());

		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->setRowTemplate('tpl.tbl_voting.html', 'Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting');
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));
		$this->setExternalSorting(true);
		$this->initColums();
		$this->addFilterItems();
		$this->parseData();
	}


	//	protected function saveSorting() {
	//		print_r('sorting: ');
	//		var_dump($_POST['position']);
	//		foreach ($_POST['position'] as $k => $v) {
	//			//			$obj = ctrlmmEntryInstaceFactory::getInstanceByEntryId($v)->getObject();
	//			//			$obj->setPosition($k);
	//			//			$obj->update();
	//			print_r('posted: ');
	//			var_dump($k . ' : ' . $v . ' - ');
	//		}
	//		exit;
	//		ilUtil::sendSuccess($this->pl->txt('sorting_saved'));
	//		$this->ctrl->redirect($this, self::CMD_STANDARD);
	//	}

	protected function addFilterItems() {
		$title = new ilTextInputGUI($this->pl->txt('title'), 'title');
		$this->addAndReadFilterItem($title);

		$title = new ilTextInputGUI($this->pl->txt('question'), 'question');
		$this->addAndReadFilterItem($title);
	}


	/**
	 * @param $item
	 */
	protected function addAndReadFilterItem(ilFormPropertyGUI $item) {
		$this->addFilterItem($item);
		$item->readFromSession();
		if ($item instanceof ilCheckboxInputGUI) {
			$this->filter[$item->getPostVar()] = $item->getChecked();
		} else {
			$this->filter[$item->getPostVar()] = $item->getValue();
		}
	}


	/**
	 * @var int
	 */
	protected static $num = 1;


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		/**
		 * @var xlvoVoting $xlvoVoting
		 */
		$xlvoVoting = xlvoVoting::find($a_set['id']);
		$this->tpl->setVariable('TITLE', $xlvoVoting->getTitle());
		$this->tpl->setVariable('DESCRIPTION', $xlvoVoting->getDescription());
		$this->tpl->setVariable('QUESTION', $xlvoVoting->getQuestion());

		$voting_type = $this->getVotingType($xlvoVoting->getVotingType());
		$this->tpl->setVariable('TYPE', $voting_type);

		$voting_status = $this->getVotingStatus($xlvoVoting->getVotingStatus());
		$this->tpl->setVariable('STATUS', $voting_status);

		// Position
		$this->tpl->setVariable('SRC_IMAGE', './Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/templates/images/move.png');
		$this->tpl->setVariable('CLASS', 'ctrlmmSeparator');
		$this->tpl->setVariable('ID_OLD', 1);
		$this->tpl->setVariable('POSITION', self::$num);
		$this->tpl->setVariable('ID_NEW', 1);
		self::$num ++;

		$this->addActionMenu($xlvoVoting);
	}


	protected function initColums() {
		$this->addColumn('', 'position', '20px');
		$this->addColumn($this->pl->txt('title'), 'title');
		$this->addColumn($this->pl->txt('question'), 'question');
		$this->addColumn($this->pl->txt('type'), 'voting_type');
		$this->addColumn($this->pl->txt('status'), 'voting_status');
		$this->addColumn($this->pl->txt('common_actions'), '', '150px');
	}


	/**
	 * @param xlvoVoting $xlvoVoting
	 */
	protected function addActionMenu(xlvoVoting $xlvoVoting) {
		global $access;
		$access = new ilObjLiveVotingAccess();

		$current_selection_list = new ilAdvancedSelectionListGUI();
		$current_selection_list->setListTitle($this->pl->txt('common_actions'));
		$current_selection_list->setId('xlvo_actions_' . $xlvoVoting->getId());
		$current_selection_list->setUseImages(false);

		$this->ctrl->setParameter($this->voting_gui, xlvoVotingGUI::IDENTIFIER, $xlvoVoting->getId());
		if ($access->hasWriteAccess()) {
			$current_selection_list->addItem($this->pl->txt('edit'), xlvoVotingGUI::CMD_EDIT, $this->ctrl->getLinkTarget($this->voting_gui, xlvoVotingGUI::CMD_EDIT));
		}
		if ($access->hasDeleteAccess()) {
			$current_selection_list->addItem($this->pl->txt('delete'), xlvoVotingGUI::CMD_CONFIRM_DELETE, $this->ctrl->getLinkTarget($this->voting_gui, xlvoVotingGUI::CMD_CONFIRM_DELETE));
			$current_selection_list->addItem($this->pl->txt('reset'), xlvoVotingGUI::CMD_CONFIRM_RESET, $this->ctrl->getLinkTarget($this->voting_gui, xlvoVotingGUI::CMD_CONFIRM_RESET));
		}
		$current_selection_list->getHTML();
		$this->tpl->setVariable('ACTIONS', $current_selection_list->getHTML());
	}


	protected function parseData() {
		// Filtern
		$this->determineOffsetAndOrder();
		$this->determineLimit();

		$collection = xlvoVoting::where(array( 'obj_id' => $this->voting_gui->getObjId() ));

		$sorting_column = $this->getOrderField() ? $this->getOrderField() : 'title';
		$offset = $this->getOffset() ? $this->getOffset() : 0;

		$sorting_direction = $this->getOrderDirection();
		$num = $this->getLimit();

		$collection->orderBy($sorting_column, $sorting_direction);
		$collection->limit($offset, $num);

		foreach ($this->filter as $filter_key => $filter_value) {
			switch ($filter_key) {
				case 'title':
				case 'question':
					$collection = $collection->where(array( $filter_key => '%' . $filter_value . '%' ), 'LIKE');
					break;
			}
		}

		$this->setData($collection->getArray());
	}


	/**
	 * @param $voting_type
	 *
	 * @return string
	 */
	protected function getVotingType($voting_type) {
		$type = '';
		switch ($voting_type) {
			case xlvoVotingType::SINGLE_VOTE:
				$type = $this->pl->txt('single_vote');
				break;
			case xlvoVotingType::FREE_INPUT:
				$type = $this->pl->txt('free_input');
				break;
		}

		return $type;
	}


	/**
	 * @param $voting_status
	 *
	 * @return string
	 */
	protected function getVotingStatus($voting_status) {
		$status = '';
		switch ($voting_status) {
			case xlvoVoting::STAT_ACTIVE:
				$status = $this->pl->txt('active');
				break;
			case xlvoVoting::STAT_INACTIVE:
				$status = $this->pl->txt('inactive');
				break;
			case xlvoVoting::STAT_INCOMPLETE:
				$status = $this->pl->txt('incomplete');
				break;
		}

		return $status;
	}
}