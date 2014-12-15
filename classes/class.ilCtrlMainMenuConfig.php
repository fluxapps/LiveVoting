<?php

/**
 * Class Configuration
 */
class ilCtrlMainMenuConfig extends ActiveRecord {
    const F_CSS_PREFIX = 'css_prefix';
    const F_CSS_ACTIVE = 'css_active';
    const F_CSS_INACTIVE = 'css_inactive';
    const F_DOUBLECLICK_PREVENTION = 'doubleclick_prevention';
    const F_SIMPLE_FORM_VALIDATION = 'simple_form_validation';
    const F_REPLACE_FULL_HEADER = "replace_full_header";

    /**
     * @var array
     */
    protected static $cache = array();
    /**
     * @var array
     */
    protected static $cache_loaded = array();
    /**
     * @var bool
     */
    protected $ar_safe_read = false;

    public static function returnDbTableName() {
        return ilCtrlMainMenuPlugin::CONFIG_TABLE;
    }

    /**
     * @param $name
     *
     * @return string
     */
    public static function get($name) {
        if (! isset(self::$cache_loaded[$name])) {
            $obj = self::find($name);
            if ($obj === NULL) {
                self::$cache[$name] = NULL;
            } else {
                self::$cache[$name] = $obj->getValue();
            }
            self::$cache_loaded[$name] = true;
        }
        return self::$cache[$name];
    }
    /**
     * @param $name
     * @param $value
     *
     * @return null
     */
    public static function set($name, $value) {
        /**
         * @var $obj arConfig
         */
        $obj = self::findOrGetInstance($name);
        $obj->setValue($value);
        if (self::where(array( 'name' => $name ))->hasSets()) {
            $obj->update();
        } else {
            $obj->create();
        }
    }

    /**
     * @var string
     *
     * @db_has_field        true
     * @db_is_unique        true
     * @db_is_primary       true
     * @db_is_notnull       true
     * @db_fieldtype        text
     * @db_length           250
     */
    protected $name;
    /**
     * @var string
     *
     * @db_has_field        true
     * @db_fieldtype        text
     * @db_length           1000
     */
    protected $value;


    /**
     * @param string $value
     */
    public function setValue($value) {
        $this->value = $value;
    }
    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }
    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }
    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }


} 