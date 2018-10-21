Demand if plugin data should be removed on uninstall

### Usage

#### Composer
First add the follow to your `composer.json` file:
```json
"require": {
  "srag/removeplugindataconfirm": ">=0.1.0"
},
```

And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Hint: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an old version of an other plugin! So you should keep up to date your plugin with `composer update`.

#### Use
First declare your plugin class like follow:
```php
//...
use srag\RemovePluginDataConfirm\PluginUninstallTrait;
//...
use PluginUninstallTrait;
//...
const PLUGIN_CLASS_NAME = self::class;
const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = XRemoveDataConfirm::class;
//...
/**
 * @inheritdoc
 */
protected function deleteData()/*: void*/ {
	// TODO: Delete your plugin data in this method
}
//...
```
`XRemoveDataConfirm` is the name of your remove data confirm class.

If your plugin is a RepositoryObject use `RepositoryObjectPluginUninstallTrait` instead:
```php
//...
use srag\RemovePluginDataConfirm\RepositoryObjectPluginUninstallTrait;
//...
use RepositoryObjectPluginUninstallTrait;
//...
```

Remove also the methods `beforeUninstall`, `afterUninstall`, `beforeUninstallCustom` and `uninstallCustom` in your plugin class.

Then create a class called `XRemoveDataConfirm` in `classes/uninstall/class.XRemoveDataConfirm.php`:
```php
<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\X\Config\XConfig;
use srag\RemovePluginDataConfirm\AbstractRemovePluginDataConfirm;

/**
 * Class XRemoveDataConfirm
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy XRemoveDataConfirm: ilUIPluginRouterGUI
 */
class XRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	const PLUGIN_CLASS_NAME = ilXPlugin::class;


	/**
	 * @inheritdoc
	 */
	public function getUninstallRemovesData()/*: ?bool*/ {
		return XConfig::getUninstallRemovesData();
	}


	/**
	 * @inheritdoc
	 */
	public function setUninstallRemovesData(/*bool*/$uninstall_removes_data)/*: void*/ {
		XConfig::setUninstallRemovesData($uninstall_removes_data);
	}


	/**
     * @inheritdoc
     */
    public function removeUninstallRemovesData()/*: void*/ {
        XConfig::removeUninstallRemovesData();
    }
}

```
`ilXPlugin` is the name of your plugin class ([DICTrait](https://github.com/studer-raimann/DIC)).
Replace the `X` in `XRemoveDataConfirm` with your plugin name.
If you do not use `ActiveRecordConfig` replace in the `UninstallRemovesData` methods with your own database functions

If you use `ActiveRecordConfig` add the follow to these class:
```php
///...
use XRemoveDataConfirm;
///...
/**
 * @return bool|null
 */
public static function getUninstallRemovesData()/*: ?bool*/ {
	return self::getXValue(XRemoveDataConfirm::KEY_UNINSTALL_REMOVES_DATA, XRemoveDataConfirm::DEFAULT_UNINSTALL_REMOVES_DATA);
}


/**
 * @param bool $uninstall_removes_data
 */
public static function setUninstallRemovesData(/*bool*/$uninstall_removes_data)/*: void*/ {
	self::setBooleanValue(XRemoveDataConfirm::KEY_UNINSTALL_REMOVES_DATA, $uninstall_removes_data);
}


/**
 *
 */
public static function removeUninstallRemovesData()/*: void*/ {
	self::removeName(XRemoveDataConfirm::KEY_UNINSTALL_REMOVES_DATA);
}
//...
```

Then you need to declare some language variables like:
English:
```
removeplugindataconfirm_cancel#:#Cancel
removeplugindataconfirm_confirm_remove_data#:#Do you want to remove the %1$s data as well? At most, you just want to disable the %1$s plugin?
removeplugindataconfirm_deactivate#:#Just deactivate %1$s plugin
removeplugindataconfirm_data#:#%1$s data
removeplugindataconfirm_keep_data#:#Keep %1$s data
removeplugindataconfirm_msg_kept_data#:#The %1$s data was kept!
removeplugindataconfirm_msg_removed_data#:#The %1$s data was also removed!
removeplugindataconfirm_remove_data#:#Remove %1$s data
```
German:
```
removeplugindataconfirm_cancel#:#Abbrechen
removeplugindataconfirm_confirm_remove_data#:#Möchten Sie die %1$s-Daten auch entfernen? Allenfalls möchten Sie das %1$s-Plugin nur deaktivieren?
removeplugindataconfirm_deactivate#:#%1$s-Plugin nur deaktivieren
removeplugindataconfirm_data#:#%1$s-Daten
removeplugindataconfirm_keep_data#:#%1$s-Daten behalten
removeplugindataconfirm_msg_kept_data#:#Die %1$s-Daten wurden behalten!
removeplugindataconfirm_msg_removed_data#:#Die %1$s-Daten wurden auch entfernt!
removeplugindataconfirm_remove_data#:#Entferne %1$s-Daten
```
If you want you can modify these. The `%1$s` placeholder is the name of your plugin.

If you want to use this library, but don't want to confirm to remove data, you can disable it with add the follow to your `ilXPlugin` class:
```php
//...
const REMOVE_PLUGIN_DATA_CONFIRM = false;
//...
```
### Dependencies
* [composer](https://getcomposer.org)
* [srag/dic](https://packagist.org/packages/srag/dic)

Please use it for further development!

### Adjustment suggestions
* Adjustment suggestions by pull requests on https://git.studer-raimann.ch/ILIAS/Plugins/RemovePluginDataConfirm/tree/develop
* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/LRPDC
* Bug reports under https://jira.studer-raimann.ch/projects/LRPDC
* For external users please send an email to support-custom1@studer-raimann.ch

### Development
If you want development in this library you should install this library like follow:

Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Libraries
cd Customizing/global/plugins/Libraries
git clone git@git.studer-raimann.ch:ILIAS/Plugins/RemovePluginDataConfirm.git RemovePluginDataConfirm
```
