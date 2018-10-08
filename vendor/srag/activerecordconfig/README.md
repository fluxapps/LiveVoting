Simple ActiveRecord config for ILIAS plugins

### Usage

#### Composer
First add the follow to your `composer.json` file:
```json
"require": {
  "srag/activerecordconfig": ">=0.1.0"
},
```

If your plugin should support ILIAS 5.2 or earlier you need to require `ActiveRecord` like follow in your `composer.json` file:
```json
"autoload": {
    "classmap": [
      "../../../../../../../Services/ActiveRecord/class.ActiveRecord.php",
```
May you need to adjust the relative `ActiveRecord` path

And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Hint: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an old version of an other plugin! So you should keep up to date your plugin with `composer update`.

#### Use config
Declare your config class basically like follow:
```php
//...
use srag\ActiveRecordConfig\ActiveRecordConfig;
//...
class XConfig extends ActiveRecordConfig {
	//...
	const TABLE_NAME = "db_table_name";
	//...
	const PLUGIN_CLASS_NAME = ilXPlugin::class;
	//...
}
```
`db_table_name` is the name of your database table.
`ilXPlugin` is the name of your plugin class ([DICTrait](https://github.com/studer-raimann/DIC)).

And now add some configs:
```php
	//...
	const KEY_SOME = "some";
	//...
	const DEFAULT_SOME = "some";
	//...
	/**
	 * @return string
	 */
	public static function getSome()/*: string*/ {
		return self::getStringValue(self::KEY_SOME, self::DEFAULT_SOME);
	}

	/**
	 * @param string $some
	 */
	public static function setSome(/*string*/$some)/*: void*/ {
		self::setStringValue(self::KEY_SOME, $some);
	}
```

If you need to remove a config add:
```php
/**
 *
 */
public static function removeSome()/*: void*/ {
	self::removeName(self::KEY_SOME);
}
```

You can now access your config like `XConfig::getSome()` and set it like `XConfig::setSome("some")`.

Internally all values are stored as strings and will casted with appropriates methods.
You can define a default value, if the value is `null`.

It exists the follow datatypes:

| Datatype  | Methods                                    |
| :-------- | :----------------------------------------- |
| string    | * getStringValue<br>* setStringValue       |
| int       | * getIntegerValue<br>* setIntegerValue     |
| double    | * getDoubleValue<br>* setDoubleValue       |
| bool      | * getBooleanValue<br>* setBooleanValue     |
| timestamp | * getTimestampValue<br>* setTimestampValue |
| json      | * getJsonValue<br>* setJsonValue           |
| null      | * isNullValue<br>* setNullValue            |

The following additional methods exist:
```php
/**
 * Get all names
 *
 * @return string[] [ "name", ... ]
 */
self::getNames()/*: array*/;

/**
 * Get all values
 *
 * @return string[] [ [ "name" => value ], ... ]
 */
self::getValues()/*: array*/;

/**
 * Set all values
 *
 * @param array $configs        [ [ "name" => value ], ... ]
 * @param bool  $delete_existss Delete all exists name before
 */
self::setValues(array $configs, /*bool*/$delete_exists = false)/*: void*/;

/**
 * Remove a name
 * 
 * @param string $name Name
 */
self::removeName(/*string*/$name)/*: void*/;
```

Other `ActiveRecord` methods should be not used!

### ActiveRecordConfigGUI
Create a class `ilXConfigGUI`:
```php
//...
use srag\ActiveRecordConfig\ActiveRecordConfigGUI;
//...
class ilXConfigGUI extends ActiveRecordConfigGUI {
	//...
	const PLUGIN_CLASS_NAME = ilXPlugin::class;
	const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = XConfigFormGUI::class;
}
```
and a class `XConfigFormGUI`:
```php
//...
use srag\ActiveRecordConfig\ActiveRecordConfigFormGUI;
//...
class XConfigFormGUI extends ActiveRecordConfigFormGUI {
	//...
	const PLUGIN_CLASS_NAME = ilXPlugin::class;
	
	/**
     * @inheritdoc
     */
    protected function setForm()/*: void*/ {
        parent::setForm();
        
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
`ilXPlugin` is the name of your plugin class ([DICTrait](https://github.com/studer-raimann/DIC)).
`XConfigFormGUI` is the name of your config form gui class.


Then you need to declare some language variables like:
English:
```
activerecordconfig_configuration#:#Configuration
activerecordconfig_configuration_saved#:#Configuration saved
activerecordconfig_save#:#Save
```
German:
```
activerecordconfig_configuration#:#Konfiguration
activerecordconfig_configuration_saved#:#Konfiguration gespeichert
activerecordconfig_save#:#Speichern
```

### Migrate from your old config class

If you need to migrate from your old config class, so you need to keep your old config class in the code, so you can migrate the data

Do the follow in your old config class:
1. Rename your old config class from `XConfig` to `XConfigOld` (May simple `Old` subfix)
2. Keep the old database name in `XConfigOld`
3. Set all in `XConfigOld` to `@deprecated`
4. May refactoring also you old config class, so all code is in one class (Such as use `TABLE_NAME` const)

Do the follow in your new config class:
1. Create a new class `XConfig` with a new database new (May simple `_n` subfix)
2. Implement `XConfig` with `ActiveRecordConfig` like described above
3. Replace all usages of `XConfigOld` with `XConfig` in your code

Finally you need to add an update step to migrate your data
1. Remove the old config class database install in the `dbupdate.php` file. The old config class doesn't need anymore to be installed
2. Add the new config class database install like `XConfig::updateDB();` in the `dbupdate.php` file
3. Migrate the data from the old config class to the new config class if the old exists and delete the old in the `dbupdate.php` file
4. Add an uninstall step for both old and new config classes in `beforeUninstall` or `beforeUninstallCustom` of your plugin class. Also remove the old config database table to make sure that it also be removed if the plugin should be unistalled without update before it

Here some examples, depending how yould old config class was:

Column name based:
```php
<#2>
<?php
XConfig::updateDB();

if (\srag\DIC\DICStatic::dic()->database()->tableExists(XConfigOld::TABLE_NAME)) {
	$config_old = XConfigOld::getConfig();

 	XConfig::setSome($config_old->getSome());
	//...

	\srag\DIC\DICStatic::dic()->database()->dropTable(XConfigOld::TABLE_NAME);
}
?>
```

Key and value based (Similar to this library):
```php
<#2>
<?php
XConfig::updateDB();

if (\srag\DIC\DICStatic::dic()->database()->tableExists(XConfigOld::TABLE_NAME)) {
	foreach (XConfigOld::get() as $config) {
		/**
		 * @var XConfigOld $config
		 */
		switch($config->getName()) {
			case XConfig::KEY_SOME:
			 	XConfig::setSome($config->getValue());
				break;
			//...
			default:
				break;
		}
	}

	\srag\DIC\DICStatic::dic()->database()->dropTable(XConfigOld::TABLE_NAME);
}
?>
```

### Dependencies
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
git clone git@git.studer-raimann.ch:ILIAS/Plugins/ActiveRecordConfig.git ActiveRecordConfig
```
