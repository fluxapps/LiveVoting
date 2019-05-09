<?php

namespace LiveVoting\QuestionTypes\FreeInput;

use ilLiveVotingPlugin;
use LiveVoting\Display\Bar\xlvoBarFreeInputsGUI;
use LiveVoting\Display\Bar\xlvoBarGroupingCollectionGUI;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVotingManager2;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class xlvoFreeInputCategoriesGUI
 *
 * @package LiveVoting\QuestionTypes\FreeInput
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class xlvoFreeInputCategoriesGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;

	const TITLE = 'title';
	const VOTES = 'votes';
	/**
	 * @var bool
	 */
	private $removable = false;

	/**
	 * @var array
	 */
	protected $categories = [];


	/**
	 * xlvoFreeInputCategoriesGUI constructor.
	 *
	 * @param xlvoVotingManager2 $manager
	 */
	public function __construct(xlvoVotingManager2 $manager) {
		/** @var xlvoFreeInputCategory $category */
		foreach (xlvoFreeInputCategory::where(['voting_id' => $manager->getVoting()->getId(), 'round_id' => $manager->getPlayer()->getRoundId()])
			         ->get() as $category) {
			$this->categories[$category->getId()] = [
				self::TITLE => $category->getTitle(),
				self::VOTES => new xlvoBarGroupingCollectionGUI()
			];
		}
	}


	/**
	 * @param xlvoBarFreeInputsGUI $bar_gui
	 * @param integer              $cat_id
	 */
	public function addBar(xlvoBarFreeInputsGUI $bar_gui, $cat_id) {
		$bar_gui->setRemovable($this->isRemovable());
		$this->categories[$cat_id][self::VOTES]->addBar($bar_gui);
	}


	/**
	 * @return string
	 * @throws \ilTemplateException
	 * @throws \srag\DIC\LiveVoting\Exception\DICException
	 */
	public function getHTML() {
		$tpl = self::plugin()->template('default/QuestionTypes/FreeInput/tpl.free_input_categories.html');
		// TODO: xlvoBarGroupingCollection GUI verwenden?
		foreach ($this->categories as $cat_id => $data) {
			$cat_tpl = self::plugin()->template('default/QuestionTypes/FreeInput/tpl.free_input_category.html');
			/** @var xlvoFreeInputCategory $category */
			$cat_tpl->setVariable('ID', $cat_id);
			$cat_tpl->setVariable('TITLE', $data[self::TITLE]);
			if ($this->isRemovable()) {
				$cat_tpl->touchBlock('remove_button');
			}

			$cat_tpl->setVariable('VOTES', $data[self::VOTES]->getHTML());
//			/** @var xlvoBarFreeInputsGUI $vote */
//			foreach ($data[self::VOTES] as $vote) {
//				$cat_tpl->setCurrentBlock('vote');
//				$cat_tpl->setVariable('VOTE', $vote->getHTML());
//				$cat_tpl->parseCurrentBlock();
//			}
			$tpl->setCurrentBlock('category');
			$tpl->setVariable('CATEGORY', $cat_tpl->get());
			$tpl->parseCurrentBlock();
		}

		return $tpl->get();
	}


	/**
	 * @return bool
	 */
	public function isRemovable() {
		return $this->removable;
	}


	/**
	 * @param bool $removable
	 */
	public function setRemovable($removable) {
		$this->removable = $removable;
	}
}