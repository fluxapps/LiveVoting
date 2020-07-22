Demand if plugin data should be removed on uninstall

### Usage

#### Composer
First add the following to your `composer.json` file:
```json
"require": {
  "srag/removeplugindataconfirm": ">=0.1.0"
},
```

And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Tip: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an older or a newer version of an other plugin!

So I recommand to use [srag/librariesnamespacechanger](https://packagist.org/packages/srag/librariesnamespacechanger) in your plugin.

#### Use
First declare your plugin class like follow:
```php
//...
use srag\RemovePluginDataConfirm\LiveVoting\x\PluginUninstallTrait;
//...
use PluginUninstallTrait;
//...
/**
 * @inheritDoc
 */
protected function deleteData()/*: void*/ {
    // TODO: Delete your plugin data in this method
}
//...
```
You don't need to use `DICTrait`, it is already in use!

If your plugin is a RepositoryObject use `RepositoryObjectPluginUninstallTrait` instead:
```php
//...
use srag\RemovePluginDataConfirm\LiveVoting\x\RepositoryObjectPluginUninstallTrait;
//...
use RepositoryObjectPluginUninstallTrait;
//...
```

Remove also the methods `beforeUninstall`, `afterUninstall`, `beforeUninstallCustom` and `uninstallCustom` in your plugin class.

Expand you plugin class for installing languages of the library to your plugin
```php
...
	/**
     * @inheritDoc
     */
    public function updateLanguages(/*?array*/ $a_lang_keys = null)/*:void*/ {
		parent::updateLanguages($a_lang_keys);

		$this->installRemovePluginDataConfirmLanguages();
	}
...
```

Notice to also adjust `dbupdate.php` so it can be reinstalled if the data should already exists!

### Requirements
* ILIAS 5.3 or ILIAS 5.4
* PHP >=7.0

### Adjustment suggestions
* External users can report suggestions and bugs at https://plugins.studer-raimann.ch/goto.php?target=uihk_srsu_LRPDC
* Adjustment suggestions by pull requests via github
* Customer of studer + raimann ag: 
	* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/LRPDC
	* Bug reports under https://jira.studer-raimann.ch/projects/LRPDC
