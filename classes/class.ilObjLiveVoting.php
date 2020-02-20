<?php

/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/

require_once __DIR__ . '/../vendor/autoload.php';

use LiveVoting\Option\xlvoOption;
use LiveVoting\Pin\xlvoPin;
use LiveVoting\Player\xlvoPlayer;
use LiveVoting\Puk\Puk;
use LiveVoting\Utils\LiveVotingTrait;
use LiveVoting\Vote\xlvoVote;
use LiveVoting\Voting\xlvoVoting;
use LiveVoting\Voting\xlvoVotingConfig;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class ilObjLiveVoting
 *
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Daniel Aemmer <daniel.aemmer@phbern.ch>
 *
 * @version $Id$
 */
class ilObjLiveVoting extends ilObjectPlugin
{

    use DICTrait;
    use LiveVotingTrait;
    const PLUGIN_CLASS_NAME = ilLiveVotingPlugin::class;


    /**
     * @param int        $a_ref_id
     * @param bool|false $by_oid
     */
    function __construct($a_ref_id = 0, $by_oid = false)
    {
        parent::__construct($a_ref_id);
        /*if ($a_ref_id != 0) {
            $this->id = $a_ref_id;
            $this->doRead();
        }*/
    }


    /**
     * Get type.
     */
    final function initType()
    {
        $this->setType(ilLiveVotingPlugin::PLUGIN_ID);
    }


    /**
     * Create object
     */
    function doCreate()
    {
        $xlvoPin = new xlvoPin();
        $xlvoPuk = new Puk();
        $config = new xlvoVotingConfig();
        $config->setObjId($this->getId());
        $config->setPin($xlvoPin->getPin());
        $config->setPuk($xlvoPuk->getPin());
        $config->store();
    }


    /**
     * Read data from db
     */
    function doRead()
    {
    }


    /**
     * Update data
     */
    function doUpdate()
    {
    }


    public function doDelete()
    {

        /**
         * @var xlvoPlayer[] $players
         */
        $players = xlvoPlayer::where(array('obj_id' => $this->getId()))->get();
        foreach ($players as $player) {
            $player->delete();
        }

        /**
         * @var xlvoVoting[] $votings
         */
        $votings = xlvoVoting::where(array('obj_id' => $this->getId()))->get();
        foreach ($votings as $voting) {
            $voting_id = $voting->getId();

            /**
             * @var xlvoVote[] $votes
             */
            $votes = xlvoVote::where(array('voting_id' => $voting_id))->get();
            foreach ($votes as $vote) {
                $vote->delete();
            }

            /**
             * @var xlvoOption[] $options
             */
            $options = xlvoOption::where(array('voting_id' => $voting_id))->get();
            foreach ($options as $option) {
                $option->delete();
            }

            $voting->delete();
        }

        /**
         * @var xlvoVotingConfig $config
         */
        $config = xlvoVotingConfig::find($this->getId());
        if ($config instanceof xlvoVotingConfig) {
            $config->delete();
        }
    }


    public function renegerateVotingSorting()
    {
        $i = 1;
        /**
         * @var xlvoVoting[] $votings
         */
        $votings = xlvoVoting::where(array('obj_id' => $this->getId()))->orderBy('position', 'ASC')->get();

        foreach ($votings as $voting) {
            $voting->setPosition($i);
            $voting->store();
            $i++;
        }
    }


    /**
     * @param                 $a_target_id
     * @param                 $a_copy_id
     * @param ilObjLiveVoting $new_obj
     */
    public function doCloneObject($new_obj, $a_target_id, $a_copy_id = null)
    {

        /**
         * @var xlvoVotingConfig $config
         */
        $config = xlvoVotingConfig::find($this->getId());
        if ($config instanceof xlvoVotingConfig) {
            /**
             * @var xlvoVotingConfig $config_clone
             */
            $config_clone = $config->copy();
            $config_clone->setObjId($new_obj->getId());
            // set unique pin for cloned object
            $xlvoPin = new xlvoPin();
            $config_clone->setPin($xlvoPin->getPin());
            $xlvoPuk = new Puk();
            $config_clone->setPuk($xlvoPuk->getPin());
            $config_clone->store();
        }

        /**
         * @var xlvoPlayer $player
         * @var xlvoPlayer $player_clone
         */
        $player = xlvoPlayer::where(array('obj_id' => $this->getId()))->first();
        if ($player instanceof xlvoPlayer) {
            $player_clone = $player->copy();
            // reset active Voting in player
            $player_clone->setActiveVoting(0);
            $player_clone->setObjId($new_obj->getId());
            $player_clone->store();
        }

        /**
         * @var xlvoVoting[] $votings
         */
        $votings = xlvoVoting::where(array('obj_id' => $this->getId()))->get();
        $media_object_ids = array();
        foreach ($votings as $voting) {

            /**
             * @var xlvoVoting $voting_clone
             */
            $voting_clone = $voting->fullClone(false, false);
            $voting_clone->setObjId($new_obj->getId());
            $voting_clone->store();

            $voting_id = $voting->getId();
            $voting_id_clone = $voting_clone->getId();
            $media_objects = ilRTE::_getMediaObjects($voting_clone->getQuestion());
            if (count($media_objects) > 0) {
                $media_object_ids = array_merge($media_object_ids, array_values($media_objects));
            }

            /**
             * @var xlvoOption[] $options
             */
            $options = xlvoOption::where(array('voting_id' => $voting_id))->get();
            foreach ($options as $option) {
                /**
                 * @var xlvoOption $option_clone
                 */
                $option_clone = $option->copy();
                $option_clone->setVotingId($voting_id_clone);
                $option_clone->store();

                $option_id_clone = xlvoOption::where(array('voting_id' => $voting_id_clone))->last()->getId();

                /**
                 * @var xlvoVote[] $votes
                 */
                $votes = xlvoVote::where(array('voting_id' => $voting_id))->get();
                foreach ($votes as $vote) {
                    /**
                     * @var xlvoVote $vote_clone
                     */
                    $vote_clone = $vote->copy();
                    $vote_clone->setVotingId($voting_id_clone);
                    $vote_clone->setOptionId($option_id_clone);
                    //					$vote_clone->store(); // CURRENTLY VOTES WILL NOT BE CLONED
                }
            }
        }
        $new_obj->renegerateVotingSorting();
        foreach ($media_object_ids as $media_object_id) {
            ilObjMediaObject::_saveUsage($media_object_id, 'dcl:html', $new_obj->getId());
        }
    }
}
