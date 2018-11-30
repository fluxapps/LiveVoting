# Changelog

## [0.14.2]
- PHPVersionChecker fix cache

## [0.14.1]
- PHPVersionChecker cache

## [0.14.0]
- PHPVersionChecker

## [0.13.5]
- DICStatic::clearCache

## [0.13.4]
- New getHTML

## [0.13.3]
- New getHTML

## [0.13.2]
- Supports output ilAdvancedSelectionListGUI

## [0.13.1]
- Supports output ilModalGUI

## [0.13.0]
- getHTML in OutputInterface

## [0.12.0]
- Supports output Component
- Supports new ILIAS 5.4 DIC services
- Separate DIC implementation for ILIAS versions
- New methods an constants in VersionInterface

## [0.11.0]
- New OutputInterface

## [0.10.3]
- Fix Pluginable interface

## [0.10.2]
- Fix Pluginable interface

## [0.10.1]
- Fix Pluginable interface

## [0.10.0]
- Pluginable interface

## [0.9.2]
- Fix readme

## [0.9.1]
- Fix

## [0.9.0]
- VersionInterface
- `mailMimeTransportFactory`
- Improve translate, so it correct handle missing language strings and autoload language modules
- Rename`template` to `mainTemplate` in DICInterface

## [0.8.11]
- Add `@since`

## [0.8.10]
- Update readme

## [0.8.9]
- Added dependencies to readme

## [0.8.8]
- Add `@author` to classes

## [0.8.7]
- Fix output

## [0.8.6]
- PHP7 comments

## [0.8.5]
- PHP7 comments

## [0.8.4]
- Update `translate` hints

## [0.8.3]
- Hint 'Because `translate` use `vsprintf`, you need to escape `%` with `%%` in your language strings if it is no placeholder!'

## [0.8.2]
- Hint 'Because `translate` use `vsprintf`, you need to escape `%` with `%%` if it is no placeholder!'

## [0.8.1]
- Update PHPDoc

## [0.8.0]
- Supports output JSON
- Exception: Class {get_class($value)} is not supported for output!

## [0.7.3]
- Update PHPDoc and visibility

## [0.7.2]
- Update readme and PHPDoc
- Exception: Class $plugin_class_name not extends ilPlugin!

## [0.7.1]
- Fix wrong DICInterface

## [0.7.0]
- PluginInterface
- Some sub namespaces

## [0.6.0]
- Supports output ilTable2GUI
- Better DICException throws declare
- Remove `DICCache` and merge it with `DICStatic`
- Logs if `$plugin_class_name::getInstance` not exists

## [0.5.6]
- Exception: Please use the placeholders feature and not direct `sprintf` or `vsprintf` in your code!

## [0.5.5]
- Update readme

## [0.5.4]
- Update readme

## [0.5.3]
- Mark some methods as `final`

## [0.5.2]
- Use always latest version of DIC

## [0.5.1]
- Supports output ilConfirmationGUI

## [0.5.0]
- Supports output ilPropertyFormGUI

## [0.4.0]
- DICStatic

## [0.3.0]
- Rename some DIC functions:
  - `lng` to `language`
  - `tpl` to `template`

## [0.2.3]
- Adjustment suggestions

## [0.2.2]
- Use default if language text is MISSING

## [0.2.1]
- Correct use of namespace

## [0.2.0]
- Output html

## [0.1.2]
- Added changelog file

## [0.1.1]
- Ignore vendor folder
- Correct composer version handling

## [0.1.0]
- First version
