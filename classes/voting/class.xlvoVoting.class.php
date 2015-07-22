<?php


/**
 * 
 */
class xlvoVoting {

    /**
     * 
     */
    public function __construct() {
    }

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $ref_id;

    /**
     * @var string
     */
    public $question;

    /**
     * @var int
     */
    public $voting_type;

    /**
     * @var livoVotingOption []
     */
    public $voting_options;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $title;


}