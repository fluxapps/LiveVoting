## Usage

### Composer

First add the following to your `composer.json` file:

```json
"require": {
  "srag/activerecordconfig": ">=0.1.0"
},
```

And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Tip: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an older or a newer version of an other plugin!

So I recommand to use [srag/librariesnamespacechanger](https://packagist.org/packages/srag/librariesnamespacechanger) in your plugin.

## Config ActiveRecord

First you need to init the active record class with your own table name and fields with your own repository and factory. Please call it very early in your plugin code

```php
...
use srag\ActiveRecordConfig\LiveVoting\x\Config\AbstractFactory;
use srag\ActiveRecordConfig\LiveVoting\x\Config\AbstractRepository;
...
final class Repository extends AbstractRepository
{
    ...
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Repository constructor
     */
    protected function __construct()
    {
        parent::__construct();
    }


    /**
     * @inheritDoc
     *
     * @return Factory
     */
    public function factory() : AbstractFactory
    {
        return Factory::getInstance();
    }


    /**
     * @inheritDoc
     */
    protected function getTableName() : string
    {
        return ilXPlugin::PLUGIN_ID . "_config";
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        return [
            ...
        ];
    }
}
```

```php
...
use srag\ActiveRecordConfig\LiveVoting\x\Config\AbstractFactory;
...
final class Factory extends AbstractFactory
{
    ...
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Factory constructor
     */
    protected function __construct()
    {
        parent::__construct();
    }
}
```

Add an update step to your `dbupdate.php`

```php
...
<#x>
<?php
\srag\Plugins\x\Config\Repository::getInstance()->installTables();
?>
```

and not forget to add an uninstaller step in your plugin class too

```php
self::config()->dropTables();
```

Fields are an array like

```php
[
    Config::KEY_SOME => Config::TYPE_STRING
];
```

You can define a default value, if the value is empty:

```php
[
    Config::KEY_SOME => [Config::TYPE_STRING, Config::DEFAULT_SOME]
]
```

If you use the JSON datatype, you can decide if you want assoc objects or not:

```php
[
    Config::KEY_SOME => [Config::TYPE_JSON, Config::DEFAULT_SOME, true]
]
```

It exists the follow datatypes:

| Datatype       | PHP type   |
| :------------- | :--------- |
| TYPE_STRING    | string     |
| TYPE_INTEGER   | integer    |
| TYPE_DOUBLE    | double     |
| TYPE_BOOLEAN   | bool       |
| TYPE_TIMESTAMP | integer    |
| TYPE_DATETIME  | ilDateTime |
| TYPE_JSON      | mixed      |
