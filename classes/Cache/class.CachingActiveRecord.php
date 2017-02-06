<?php
/**
 * Created by PhpStorm.
 * User: nschaefli
 * Date: 10/5/16
 * Time: 8:42 AM
 */

namespace LiveVoting\Cache;

require_once('./Services/ActiveRecord/class.ActiveRecord.php');
require_once './Services/ActiveRecord/Connector/class.arConnector.php';

abstract class CachingActiveRecord extends \ActiveRecord
{
    /**
     * CachingActiveRecord constructor.
     * @param int $primary_key
     * @param \arConnector|NULL $connector
     */
    public function __construct($primary_key = 0, \arConnector $connector = NULL)
    {
        $arConnector = $connector;
        if(is_null($arConnector))
            $arConnector = new arConnectorCache(
                new \arConnectorDB()
            );

        parent::__construct($primary_key, $arConnector);
    }
}