<?php

namespace srag\CustomInputGUIs\LiveVoting\GlyphGUI;

use ilGlyphGUI;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class GlyphGUI
 *
 * @package srag\CustomInputGUIs\LiveVoting\GlyphGUI
 *
 * @deprecated
 */
class GlyphGUI extends ilGlyphGUI
{

    use DICTrait;

    /**
     * @inheritDoc
     *
     * Get glyph html
     *
     * @param string $a_glyph glyph constant
     * @param string $a_text  text representation
     *
     * @return string html
     *
     * @deprecated
     */
    static function get(/*string*/ $a_glyph, /*string*/ $a_text = "") : string
    {
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
     *
     * @deprecated
     */
    static function gets(string $a_glyph) : string
    {
        self::$map[$a_glyph]['class'] = 'glyphicons glyphicons-' . $a_glyph;

        return parent::get($a_glyph, '') . ' ';
    }
}
