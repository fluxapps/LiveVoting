Use all ILIAS globals in your class

### Usage

#### Composer
First add the follow to your `composer.json` file:
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
self::dic();
```

For instance you can access the ilCtrl global like:
```php
/**
 * @return ilCtrl
 */
self::dic()->ctrl();
```

You can access the plugin class:
```php
/**
 * Get ilPlugin instance
 * 
 * @return ilPlugin ilPlugin instance of your plugin
 */
self::pl();
```

For plugin dir use:
```php
/**
 * Get plugin directory
 * 
 * @return string Plugin directory
 */
self::directory();
```

For Output html use:
```php
/**
 * Output html
 * 
 * @param string|ilTemplate|ilConfirmationGUI|ilPropertyFormGUI $html HTML code or ilTemplate instance
 * @param bool                                                  $main Display main skin?
 */
self::output($html, $main = true);
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
 */
self::template($template, $remove_unknown_variables = true, $remove_empty_blocks = true, $plugin = true);
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
 */
self::translate($key, $module = "", $placeholders = [], $plugin = true, $lang = "", $default = "MISSING %s");
```

Should you get an exception like `Warning: sprintf(): Too few arguments`, please use the placeholders feature and not direct `sprintf` in your code.

If you really need DICTrait outside a class (For instance in `dbupdate.php`), use `DICStatic::dic()`

#### Clean up
You can now remove all usages of ILIAS globals in your class and replace it with this library.

#### Other tips
- Use `__DIR__`
- Use not `__FILE__`
- Use not `dirname(dirname(..))`, use `../../`
- Use also `__DIR__` for `Customizing/..` and use relative paths from your class perspective (Except in `dbupdate.php`)
- Try to avoid use `$pl`

#### Requirements
This library should works with every ILIAS version provided the features are supported.

### Adjustment suggestions
* Adjustment suggestions by pull requests on https://git.studer-raimann.ch/ILIAS/Plugins/DIC/tree/develop
* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/LDIC
* Bug reports under https://jira.studer-raimann.ch/projects/LDIC
* For external developers please send an email to support-custom1@studer-raimann.ch

### Development
If you want development in this library you should install this library like follow:

Start at your ILIAS root directory 
```bash
mkdir -p Customizing/global/plugins/Libraries/  
cd Customizing/global/plugins/Libraries/  
git clone git@git.studer-raimann.ch:ILIAS/Plugins/DIC.git DIC
```
