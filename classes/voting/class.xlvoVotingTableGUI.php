<?php

require_once('./Services/Table/classes/class.ilTable2GUI.php');
require_once('./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php');
require_once('./Services/Form/classes/class.ilMultiSelectInputGUI.php');

/**
 * Class xlvoVotingTableGUI
 *
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 * @version 1.0.0
 */
class xlvoVotingTableGUI extends ilTable2GUI {

	const TBL_ID = 'tbl_xlvo';
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;
	/**
	 * @var xlvoVotingGUI
	 */
	protected $voting_gui;
	/**
	 * @var array
	 */
	protected $filter = array();


	public function  __construct(xlvoVotingGUI $a_parent_obj, $a_parent_cmd) {
		/**
		 * @var $ilCtrl ilCtrl
		 */
		global $ilCtrl;
		$this->voting_gui = $a_parent_obj;
		$this->ctrl = $ilCtrl;
		$this->pl = ilLiveVotingPlugin::getInstance();

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


	protected function addFilterItems() {
		$title = new ilTextInputGUI($this->pl->txt('title'), 'title');
		$this->addAndReadFilterItem($title);

		$title = new ilTextInputGUI($this->pl->txt('description'), 'description');
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
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		$xlvoVoting = xlvoVoting::find($a_set['id']);
		$this->tpl->setVariable('TITLE', $xlvoVoting->getTitle());
		$this->tpl->setVariable('DESCRIPTION', $xlvoVoting->getDescription());

		$this->addActionMenu($xlvoVoting);
	}


	protected function initColums() {
		$this->addColumn($this->pl->txt('title'), 'title');
		$this->addColumn($this->pl->txt('description'), 'description');
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
		}
		$current_selection_list->getHTML();
//		exit;
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
						case 'description':
							$collection->where(array( $filter_key => '%' . $filter_value . '%' ), 'LIKE');
							break;
					}
				}

		$this->setData($collection->getArray());
	}
}