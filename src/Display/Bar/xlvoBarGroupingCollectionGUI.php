<?php

namespace LiveVoting\Display\Bar;

use ilException;

/**
 * Class xlvoBarGroupingCollectionGUI
 *
 * The grouping collection groups elements by the freeinput text and shows an corresponding badge to
 * indicate the number of times the answer got submitted by the voters.
 *
 * Please not that this class is only compatible with the xlvoBarFreeInputsGUI bar type.
 *
 * @package LiveVoting\Display\Bar
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @version 1.0.0
 * @since   3.5.0
 */
final class xlvoBarGroupingCollectionGUI extends xlvoBarCollectionGUI {

	const TEMPLATE_BLOCK_NAME = 'bar';
	/**
	 * @var xlvoBarFreeInputsGUI[] $bars
	 */
	private $bars = [];
	/**
	 * @var bool $rendered
	 */
	private $rendered = false;
	/**
	 * @var bool $sorted
	 */
	private $sorted = false;


	/**
	 * Adds a bar to the grouping collection.
	 *
	 * @param xlvoGeneralBarGUI $bar_gui
	 *
	 * @return void
	 * @throws ilException If the bars are already rendered or the given type is not compatible
	 *                     with the collection.
	 */
	public function addBar(xlvoGeneralBarGUI $bar_gui) {
		$this->checkCollectionState();

		if ($bar_gui instanceof xlvoBarFreeInputsGUI) {
			$this->bars[] = $bar_gui;
		} else {
			throw new ilException('$bar_gui must a type of xlvoBarFreeInputsGUI.');
		}
	}


	/**
	 * @param bool $enabled
	 */
	public function sorted($enabled) {
		$this->sorted = $enabled;
	}


	/**
	 * Render the template.
	 * After the rendering process the bar object frees all resources and is no longer usable.
	 *
	 * @return string
	 * @throws ilException If the bars are already rendered.
	 */
	public function getHTML() {

		$this->checkCollectionState();

		$this->renderVotersAndVotes();

		$bars = NULL;
		if ($this->sorted) {
			$bars = $this->sortBarsByFrequency($this->bars);
		} else {
			$bars = $this->makeUniqueArray($this->bars);
		}

		//render the bars on demand
		foreach ($bars as $bar) {
			$count = $this->countItemOccurence($this->bars, $bar);
			$this->renderBar($bar, $count);
		}

		unset($this->bars);
		$this->rendered = true;

		return $this->tpl->get();
	}


	/**
	 * Add a solution to the collection.
	 *
	 * @param string $html The html which should be displayed as the solution.
	 *
	 * @return void
	 * @throws ilException If the bars are already rendered.
	 */
	public function addSolution($html) {
		$this->checkCollectionState();
		parent::addSolution($html);
	}


	/**
	 * Set the total votes of this question.
	 *
	 * @param int $total_votes The total votes done.
	 *
	 * @return void
	 * @throws ilException If the bars are already rendered.
	 */
	public function setTotalVotes($total_votes) {
		$this->checkCollectionState();
		parent::setTotalVotes($total_votes);
	}


	/**
	 * Indicates if the voters should be shown by the collection.
	 *
	 * @param bool $show_total_votes Should the total votes be displayed?
	 *
	 * @return void
	 * @throws ilException If the bars are already rendered.
	 */
	public function setShowTotalVotes($show_total_votes) {
		$this->checkCollectionState();
		parent::setShowTotalVotes($show_total_votes);
	}


	/**
	 * Set the number of the voter participating at this question.
	 *
	 * @param int $total_voters The number of voters.
	 *
	 * @return void
	 * @throws ilException If the bars are already rendered.
	 */
	public function setTotalVoters($total_voters) {
		$this->checkCollectionState();
		parent::setTotalVoters($total_voters);
	}


	/**
	 * @param bool $show_total_voters
	 *
	 * @return void
	 * @throws ilException If the bars are already rendered.
	 */
	public function setShowTotalVoters($show_total_voters) {
		$this->checkCollectionState();
		parent::setShowTotalVoters($show_total_voters);
	}


	/**
	 * This method renders the bars.
	 *
	 * @param xlvoBarFreeInputsGUI $bar   The bar which should be rendered into the template.
	 * @param int                  $count The times the bar got grouped.
	 *
	 * @return void
	 */
	private function renderBar(xlvoBarFreeInputsGUI $bar, $count) {
		$bar->setOccurrences($count);

		$this->tpl->setCurrentBlock(self::TEMPLATE_BLOCK_NAME);
		$this->tpl->setVariable('BAR', $bar->getHTML());
		$this->tpl->parseCurrentBlock();
	}


	/**
	 * Count the occurrences of bar within the given collection of bar.
	 *
	 * @param xlvoBarFreeInputsGUI[] $bars The collection which should be searched
	 * @param xlvoBarFreeInputsGUI   $bar
	 *
	 * @return int The times bar was found in bars.
	 */
	private function countItemOccurence(array $bars, xlvoBarFreeInputsGUI $bar) {
		$count = 0;
		foreach ($bars as $entry) {
			if ($bar->equals($entry)) {
				$count ++;
			}
		}

		return $count;
	}


	/**
	 * Filter the array by freetext input.
	 * The filter is case insensitive.
	 *
	 * @param xlvoBarFreeInputsGUI[] $bars The array which should be filtered.
	 *
	 * @return xlvoBarFreeInputsGUI[] The new array which contains only unique bars.
	 */
	private function makeUniqueArray(array $bars) {
		/**
		 * @var xlvoBarFreeInputsGUI $filter
		 */
		$uniqueBars = [];

		while (count($bars) > 0) {
			$bar = reset($bars);
			$bars = array_filter($bars, function ($item) use ($bar) {
				return !$bar->equals($item);
			});
			$uniqueBars[] = $bar;
		}

		return $uniqueBars;
	}


	/**
	 * Checks the collection state. If the collection is no longer
	 * usable a ilException is thrown. This method does nothing, if
	 * the collection is ready to go.
	 *
	 * @return void
	 * @throws ilException If the bars are already rendered.
	 */
	private function checkCollectionState() {
		if ($this->rendered) {
			throw new ilException("The bars are already rendered, therefore the collection can't be modified or rendered.");
		}
	}


	/**
	 * Creates a copy with unique elements of the supplied array and sorts the content afterwards.
	 * The current sorting is descending.
	 *
	 * @param xlvoBarFreeInputsGUI[] $bars The array of bars which should be sorted.
	 *
	 * @return xlvoBarFreeInputsGUI[] Descending sorted array.
	 */
	private function sortBarsByFrequency(array $bars) {
		//dirty -> should be optimised in the future.

		$unique = $this->makeUniqueArray($bars);

		//[[count, bar], [count, bar]]
		$result = [];

		foreach ($unique as $item) {
			$result[] = [ $this->countItemOccurence($bars, $item), $item ];
		}

		//sort elements
		usort($result, function ($array1, $array2) {
			if ($array1[0] == $array2[0]) {
				return 0;
			}

			if ($array1[0] < $array2[0]) {
				return 1;
			}

			return - 1;
		});

		//flatten the array to the bars
		$sortedResult = [];

		foreach ($result as $entry) {
			$sortedResult[] = $entry[1];
		}

		return $sortedResult;
	}
}
