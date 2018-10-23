<?php

namespace srag\CustomInputGUIs\GlyphGUI;

use ilGlyphGUI;
use srag\DIC\DICTrait;

/**
 * Class GlyphGUI
 *
 * @package srag\CustomInputGUIs\GlyphGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class GlyphGUI extends ilGlyphGUI {

	use DICTrait;


	/**
	 * Get glyph html
	 *
	 * @param string $a_glyph glyph constant
	 * @param string $a_text  text representation
	 *
	 * @return string html
	 */
	static function get($a_glyph, $a_text = "") {
		if ($a_glyph == 'remove') {
			self::$map[$a_glyph]['class'] = 'glyphicon glyphicon-' . $a_glyph;
		}
		if (!isset(self::$map[$a_glyph])) {
			self::$map[$a_glyph]['class'] = 'glyphicon glyphicon-' . $a_glyph;
		}

		return parent::get($a_glyph, $a_text) . ' ';
	}


	/**
	 * @param $a_glyph
	 *
	 * @return string
	 */
	static function gets($a_glyph) {
		self::$map[$a_glyph]['class'] = 'glyphicons glyphicons-' . $a_glyph;

		return parent::get($a_glyph, '') . ' ';
	}
}
