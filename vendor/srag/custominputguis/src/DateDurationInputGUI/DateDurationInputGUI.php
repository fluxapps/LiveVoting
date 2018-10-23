<?php

namespace srag\CustomInputGUIs\DateDurationInputGUI;

use ilCalendarUtil;
use ilDateDurationInputGUI;
use ilDateTime;
use srag\DIC\DICTrait;

/**
 * Class DateDurationInputGUI
 *
 * @package srag\CustomInputGUIs\DateDurationInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class DateDurationInputGUI extends ilDateDurationInputGUI {

	use DICTrait;


	/**
	 * Check input, strip slashes etc. set alert, if input is not ok.
	 *
	 * @return boolean Input ok, true/false
	 */
	public function checkInput()/*: bool*/ {
		if ($this->getDisabled()) {
			return true;
		}
		$post = $_POST[$this->getPostVar()];
		if (!is_array($post)) {
			return false;
		}
		$start = $post["start"];
		$end = $post["end"];
		// if full day is active, ignore time format
		$format = $post['tgl'] ? 0 : $this->getDatePickerTimeFormat();
		// always done to make sure there are no obsolete values left
		$this->setStart(NULL);
		$this->setEnd(NULL);
		$valid_start = false;
		if (trim($start)) {
			$parsed = ilCalendarUtil::parseIncomingDate($start, $format);
			if ($parsed) {
				$this->setStart($parsed);
				$valid_start = true;
			}
		} else {
			if (!$this->getRequired() && !trim($end)) {
				$valid_start = true;
			}
		}
		$valid_end = false;
		if (trim($end)) {
			$parsed = ilCalendarUtil::parseIncomingDate($end, $format);
			if ($parsed) {
				$this->setEnd($parsed);
				$valid_end = true;
			}
		} else {
			if (!$this->getRequired() && !trim($start)) {
				$valid_end = true;
			}
		}
		if ($this->getStartYear()) {
			if ($valid_start
				&& $this->getStart()->get(IL_CAL_FKT_DATE, "Y") < $this->getStartYear()) {
				$valid_start = false;
			}
			if ($valid_end
				&& $this->getEnd()->get(IL_CAL_FKT_DATE, "Y") < $this->getStartYear()) {
				$valid_end = false;
			}
		}
		$valid = ($valid_start || $valid_end);
		if ($valid
			&& $this->getStart()
			&& $this->getEnd()
			&& ilDateTime::_after($this->getStart(), $this->getEnd())) {
			$valid = false;
		}
		if (!$valid) {
			$this->invalid_input_start = $start;
			$this->invalid_input_end = $end;
			$_POST[$this->getPostVar()]["start"] = NULL;
			$_POST[$this->getPostVar()]["end"] = NULL;
			$this->setAlert(self::dic()->language()->txt("form_msg_wrong_date"));
		} else {
			if ($this->getStart() && $valid_start) {
				$post_format = $format ? IL_CAL_DATETIME : IL_CAL_DATE;
				$_POST[$this->getPostVar()]["start"] = $this->getStart()->get($post_format);
				unset($_POST[$this->getPostVar()]["tgl"]);
			} else {
				$_POST[$this->getPostVar()]["start"] = NULL;
			}
			if ($this->getEnd() && $valid_end) {
				$post_format = $format ? IL_CAL_DATETIME : IL_CAL_DATE;
				$_POST[$this->getPostVar()]["end"] = $this->getEnd()->get($post_format);
				unset($_POST[$this->getPostVar()]["tgl"]);
			} else {
				$_POST[$this->getPostVar()]["end"] = NULL;
			}
		}

		return $valid;
	}


	/**
	 * @return array
	 */
	public function getValue()/*: array*/ {
		$start = $this->getStart() ? $this->getStart()->get(IL_CAL_UNIX) : 0;
		$end = $this->getEnd() ? $this->getEnd()->get(IL_CAL_UNIX) : 0;

		return [
			'start' => $start,
			'end' => $end
		];
	}
}
