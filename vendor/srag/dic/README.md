Use all ILIAS globals in your class

### Usage

#### Composer
First add the following to your `composer.json` file:
```json
"require": {
  "srag/dic": ">=0.1.0"
},
```
And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Hint: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an old version of an other plugin! So you should keep up to date your plugin with `composer update`.

#### Use trait
Declare your class like follow:
```php
//...
use srag\DIC\DICTrait;
//...
class x {
	//...
	use DICTrait;
	//...
	const PLUGIN_CLASS_NAME = ilXPlugin::class;
	//...
}
```
`ilXPlugin` is the name of your plugin class.

#### Use
You can now access the DIC interface, in instance and in static places:
```php
/**
 * Get DIC interface
 * 
 * @return DICInterface DIC interface
 */
self::dic()/*: DICInterface*/;
```

For instance you can access the ilCtrl global like:
```php
/**
 * @return ilCtrl
 */
self::dic()->ctrl()/*: ilCtrl*/;
```

You can access the plugin interface, in instance and in static places:
```php
/**
 * Get plugin interface
 * 
 * @return PluginInterface Plugin interface
 *
 * @throws DICException
 */
self::plugin()/*: PluginInterface*/;
```

The plugin interface has the follow methods:

For plugin dir use:
```php
/**
 * Get plugin directory
 * 
 * @return string Plugin directory
 */
self::plugin()->directory()/*: string*/;
```

For output html, gui or json use:
```php
/**
 * Output HTML, GUI or JSON
 * 
 * @param string|ilTemplate|ilConfirmationGUI|ilPropertyFormGUI|ilTable2GUI|int|double|bool|array|stdClass|JsonSerializable $html HTML code or some gui instance
 * @param bool                                                                                                                   $main Display main skin?
 *
 * @throws DICException
 */
self::plugin()->output($value, $main = true)/*: void*/;
```

For get a template use:
```php
/**
 * Get a template
 * 
 * @param string $template                 Template path
 * @param bool   $remove_unknown_variables Should remove unknown variables?
 * @param bool   $remove_empty_blocks      Should remove empty blocks?
 * @param bool   $plugin                   Plugin template or ILIAS core template?
 *
 * @return ilTemplate ilTemplate instance
 *
 * @throws DICException
 */
self::plugin()->template(/*string*/$template, /*bool*/$remove_unknown_variables = true, /*bool*/$remove_empty_blocks = true, /*bool*/$plugin = true)/*: ilTemplate*/;
```

For translate use:
```php
/**
 * Translate text
 * 
 * @param string $key          Language key
 * @param string $module       Language module
 * @param array  $placeholders Placeholders in your language texst to replace with vsprintf
 * @param bool   $plugin       Plugin language or ILIAS core language?
 * @param string $lang         Possibly specific language, otherwise current language, if empty
 * @param string $default      Default text, if language key not exists
 *
 * @return string Translated text
 *
 * @throws DICException
 */
self::plugin()->translate(/*string*/$key, /*string*/$module = "", array $placeholders = [], /*bool*/$plugin = true, /*string*/$lang = "", /*string*/$default = "MISSING %s")/*: string*/;
```
Hints:
- Please use not more manually `sprintf` or `vsprintf`, use the `$placeholders` parameter. Otherwise you will get an appropriate DICException thrown. This because `translate` use always `vsprintf` and if you pass to few palceholders, `vsprintf` will throw an Exception.
- Because `translate` use `vsprintf`, you need to escape `%` with `%%` in your language strings if it is no placeholder!

If you really need the ILIAS plugin object use but avoid this:
```php
/**
 * Get ILIAS plugin object instance
 *
 * @return ilPlugin ILIAS plugin object instance
 *
 * @deprecated Please avoid to use ILIAS plugin object instance and instead use methods in this class!
 */
self::plugin()->getPluginObject()/*: ilPlugin*/;
```

You can access ILIAS version informations, in instance and in static places:
```php
/**
 * Get version interface
 * 
 * @return VersionInterface Version interface
 */
self::version()/*: VersionInterface*/;
```

If you really need DICTrait outside a class (For instance in `dbupdate.php`), use `DICStatic::dic()` or `DICStatic::plugin(ilXPlugin::class)`.

#### Clean up
You can now remove all usages of ILIAS globals in your class and replace it with this library.
Please avoid to store in variables or class variables.

#### Other tips
- Use `__DIR__`
- Use not `__FILE__`
- Use not `dirname(dirname(..))`, use `../../`
- Use also `__DIR__` for `Customizing/..` and use relative paths from your class perspective (Except in `dbupdate.php`)
- Try to avoid use `$pl`

### Dependencies
* PHP >=5.6
* [composer](https://getcomposer.org)

Please use it for further development!

### Adjustment suggestions
* Adjustment suggestions by pull requests on https://git.studer-raimann.ch/ILIAS/Plugins/DIC/tree/develop
* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/LDIC
* Bug reports under https://jira.studer-raimann.ch/projects/LDIC
* For external users please send an email to support-custom1@studer-raimann.ch

### Development
If you want development in this library you should install this library like follow:

Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Libraries
cd Customizing/global/plugins/Libraries
git clone -b develop git@git.studer-raimann.ch:ILIAS/Plugins/DIC.git DIC
```
