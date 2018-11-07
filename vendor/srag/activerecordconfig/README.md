Simple ActiveRecord config for ILIAS plugins

### Usage

#### Composer
First add the following to your `composer.json` file:
```json
"require": {
  "srag/activerecordconfig": ">=0.1.0"
},
```

If your plugin should support ILIAS 5.2 or earlier you need to require some ILIAS core classes like follow in your `composer.json` file:
```json
"autoload": {
    "classmap": [
      "../../../../../../../Services/ActiveRecord/class.ActiveRecord.php",
      "../../../../../../../Services/Component/classes/class.ilPluginConfigGUI.php",
      "../../../../../../../Services/Form/classes/class.ilPropertyFormGUI.php",
      "../../../../../../../Services/Table/classes/class.ilTable2GUI.php",
```
May you need to adjust the relative `ActiveRecord` path

And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Hint: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an old version of an other plugin! So you should keep up to date your plugin with `composer update`.

#### Use config
Declare your config class basically like follow:
```php
//...
namespace srag\Plugins\X\Config
//...
use srag\ActiveRecordConfig\ActiveRecordConfig;
//...
class Config extends ActiveRecordConfig {
    //...
    const TABLE_NAME = "db_table_name";
    //...
    const PLUGIN_CLASS_NAME = ilXPlugin::class;
    //...
}
```
`db_table_name` is the name of your database table.
`ilXPlugin` is the name of your plugin class ([DICTrait](https://github.com/studer-raimann/DIC)).
You don't need to use `DICTrait`, it is already in use!

And now add some configs:
```php
    //...
    const KEY_SOME = "some";
    //...
    /**
     * @var array
     */
     protected static $fields = [
		self::KEY_SOME => [ self::TYPE_STRING, self::DEFAULT_SOME ]
     ];
     //...
```

You can define a default value, if the value is empty:
```php
    //...
    const DEFAULT_SOME = "some";
    //...
    self::KEY_SOME => [ self::TYPE_STRING, self::DEFAULT_SOME ]
    //...
```

If you use the JSON datatype, you can decide if you want assoc objects or not:
```php
    //...
	self::KEY_SOME => [ self::TYPE_JSON, self::DEFAULT_SOME, true ]
    //...
```

You can now access your config like `Config::getField(Config::KEY_SOME)` and set it like `Config::setField(Config::KEY_SOME, "some")`.
If you need to remove a config, do `Config::removeField(Config::KEY_SOME)`.

You can get all configs by `Config::getFields()` and set by `Config::setFields(array $fields)`.

Internally all values are stored as strings and will be mapped with appropriates datatype to a PHP datatype.
    
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

Other `ActiveRecord` methods should be not used!

### ActiveRecordConfigGUI
This class is experimental. Use it with care!
It only supports a config with an `ilPropertyFormGUI` or an `ilTable2GUI`!

Create a class `ilXConfigGUI`:
```php
//...
use srag\ActiveRecordConfig\ActiveRecordConfigGUI;
//...
class ilXConfigGUI extends ActiveRecordConfigGUI {
    //...
    const PLUGIN_CLASS_NAME = ilXPlugin::class;
    /**
     * @var array
     */
    protected static $tabs = [ self::TAB_CONFIGURATION => ConfigFormGUI::class ];
}
```

Declare in `$tabs` your tabs. The key is the tab id and the value your config tab class.

A config tab class can be either a class `ConfigFormGUI`:
```php
//...
namespace srag\Plugins\X\Config
//...
use srag\ActiveRecordConfig\ActiveRecordConfigFormGUI;
//...
class ConfigFormGUI extends ActiveRecordConfigFormGUI {
    //...
    const PLUGIN_CLASS_NAME = ilXPlugin::class;
    
    /**
     * @inheritdoc
     */
    protected function initForm()/*: void*/ {
        parent::initForm();
        
        // TODO: Fill your config form
    }


    /**
     * @inheritdoc
     */
    public function updateConfig()/*: void*/ {
        // TODO: Update your config
    }
}
```
or a class `ConfigTableGUI`:
```php
//...
namespace srag\Plugins\X\Config
//...
use srag\ActiveRecordConfig\ActiveRecordConfigTableGUI;
//...
class ConfigTableGUI extends ActiveRecordConfigTableGUI {
    //...
    const PLUGIN_CLASS_NAME = ilXPlugin::class;
    
    /**
     *
     */
    protected function initTable()/*: void*/ {
        parent::initTable();

        // TODO: Set your config template file
    }
    
    
    /**
     *
     */
    public function initFilter() {
        parent::initFilter();
        
        // TODO: Set your config filter
    }


    /**
     *
     */
    protected function initData()/*: void*/ {
        // TODO: Set your config data
    }


    /**
     *
     */
    protected function initColumns()/*: void*/ {
        // TODO: Set your config columns
    }


    /**
     * @param array $row
     */
    protected function fillRow(/*array*/ $row)/*: void*/ {
        // TODO: Set your config row
    }
}
```

`ilXPlugin` is the name of your plugin class ([DICTrait](https://github.com/studer-raimann/DIC)).
`ConfigFormGUI` is the name of your config form gui class.
You don't need to use `DICTrait`, it is already in use!

Your config tab class becomes automatic a command `configure_tabId`.
`ConfigFormGUI` becomes additionally the command `updateConfigure_tabId`.
`ConfigTableGUI` becomes additionally the command `applyFilter_tabId` and `resetFilter_tabId`.
You dont't need to declare it self.
You can also override this commands.

You can add custom commands in `ilXConfigGUI` if you nedd:
```php
    //...
    const CMD_COMMAND = "command";
    //...
    /**
     * @var array
     */
    protected static $custom_commands = [
        self::CMD_COMMAND
    ];
    //...
    protected function command()/*: void*/ {
        // TODO: Implement your custom command
    }
//...
```

Then you need to declare some language variables like:
English:
```
config_configuration#:#Configuration
config_configuration_saved#:#Configuration saved
config_save#:#Save
```
German:
```
config_configuration#:#Konfiguration
config_configuration_saved#:#Konfiguration gespeichert
config_save#:#Speichern
```

For each tab you need to declare additionally a language variable:
```
config_tabid#:#Something
```

There exists some help functions in `ilXConfigGUI`:

```php
/**
 * @param string $tab_id
 *
 * @return string
 */
$this->getCmdForTab(/*string*/ $tab_id)/*: void*/;

/**
 * @param string $tab_id
 */
$this->redirectToTab(/*string*/ $tab_id);/*: void*/
```

### Migrate from your old config class

If you need to migrate from your old config class, so you need to keep your old config class in the code, so you can migrate the data

Do the follow in your old config class:
1. Rename your old config class from `Config` to `ConfigOld` (May simple `Old` subfix)
2. Keep the old database name in `ConfigOld`
3. Set all in `ConfigOld` to `@deprecated`
4. May refactoring also you old config class, so all code is in one class (Such as use `TABLE_NAME` const)

Do the follow in your new config class:
1. Create a new class `Config` with a new database new (May simple `_n` subfix)
2. Implement `Config` with `ActiveRecordConfig` like described above
3. Replace all usages of `ConfigOld` with `Config` in your code

Finally you need to add an update step to migrate your data
1. Remove the old config class database install in the `dbupdate.php` file. The old config class doesn't need anymore to be installed
2. Add the new config class database install like `Config::updateDB();` in the `dbupdate.php` file
3. Migrate the data from the old config class to the new config class if the old exists and delete the old in the `dbupdate.php` file
4. Add an uninstall step for both old and new config classes in `beforeUninstall` or `beforeUninstallCustom` of your plugin class. Also remove the old config database table to make sure that it also be removed if the plugin should be unistalled without update before it

Here some examples, depending how yould old config class was:

Column name based:
```php
<#2>
<?php
\srag\Plugins\X\Config\Config::updateDB();

if (\srag\DIC\DICStatic::dic()->database()->tableExists(\srag\Plugins\X\Config\ConfigOld::TABLE_NAME)) {
    \srag\Plugins\X\Config\ConfigOld::updateDB();

    $config_old = \srag\Plugins\X\Config\ConfigOld::getConfig();

     \srag\Plugins\X\Config\Config::setField(Config::KEY_SOME, $config_old->getSome());
    //...

    \srag\DIC\DICStatic::dic()->database()->dropTable(\srag\Plugins\X\Config\ConfigOld::TABLE_NAME);
}
?>
```

Key and value based (Similar to this library):
```php
<#2>
<?php
\srag\Plugins\X\Config\Config::updateDB();

if (\srag\DIC\DICStatic::dic()->database()->tableExists(\srag\Plugins\X\Config\ConfigOld::TABLE_NAME)) {
    \srag\Plugins\X\Config\ConfigOld::updateDB();

    foreach (\srag\Plugins\X\Config\ConfigOld::get() as $config) {
        /**
         * @var \srag\Plugins\X\Config\ConfigOld $config
         */
        \srag\Plugins\X\Config\Config::setField($config->getName(), $config->getValue());
    }

    \srag\DIC\DICStatic::dic()->database()->dropTable(\srag\Plugins\X\Config\ConfigOld::TABLE_NAME);
}
?>
```

### Dependencies
* PHP >=5.6
* [composer](https://getcomposer.org)
* [srag/dic](https://packagist.org/packages/srag/dic)

Please use it for further development!

### Adjustment suggestions
* Adjustment suggestions by pull requests on https://git.studer-raimann.ch/ILIAS/Plugins/ActiveRecordConfig/tree/develop
* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/ACCONF
* Bug reports under https://jira.studer-raimann.ch/projects/ACCONF
* For external users please send an email to support-custom1@studer-raimann.ch

### Development
If you want development in this library you should install this library like follow:

Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Libraries
cd Customizing/global/plugins/Libraries
git clone -b develop git@git.studer-raimann.ch:ILIAS/Plugins/ActiveRecordConfig.git ActiveRecordConfig
```
