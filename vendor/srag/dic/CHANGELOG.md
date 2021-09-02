# Changelog

## [0.33.2]
- Use renderAsync in async ilCtrl contexts

## [0.33.1]
- Not use GeneratePluginReadme

## [0.33.0]
- ILIAS 7 support
  - imagePathResolver
  - resourceStorage
  - skills
- Remove ILIAS 5.4 support
- Min PHP 7.2
- Remove deprecated legacy fallback dic methods (mainTemplate, rbacadmin, rbacreview, rbacsystem, tree)
- Remove deprecated clearCache

## [0.32.4]
- Change utils url

## [0.32.3]
- Update urls

## [0.32.2]
- Update readme

## [0.32.1]
- `PluginVersionParameter`

## [0.32.0]
- `PluginVersionParameter`

## [0.31.5]
- Update project url

## [0.31.4]
- Remove `Ilias7PreWarn`

## [0.31.3]
- Cache `ilMMItemRepository`

## [0.31.2]
- Fix no DIC index for `ilTemplate` in `FixUITemplateInCronContext`

## [0.31.1]
- Not call `ilTemplate` constructor in `FixUITemplateInCronContext`

## [0.31.0]
- `FixUITemplateInCronContext`

## [0.30.2]
- Move doc/DESCRIPTION.md to src/LONG_DESCRIPTION.md

## [0.30.1]
- Generate readme

## [0.30.0]
- Generate readme

## [0.29.0]
- Move DevTools to separate package
- Move LibraryLanguageInstaller to separate package

## [0.28.0]
- `DataFactory`

## [0.27.0]
- `Ilias7PreWarn`

## [0.26.0]
- `DevToolsCtrl`

## [0.25.1]
- Fix

## [0.25.0]
- `rendererLoader`
- `javaScriptBinding`
- `templateFactory`
- `resourceRegistry`
- `AbstractLoaderDetector`

## [0.24.0]
- `ilCertificateActiveValidator`
- `ilObjUseBookDBRepository`
- `ilBookingReservationDBRepositoryFactory`
- `ilMMItemRepository`

## [0.23.0]
- `createOrUpdateTable`
- `multipleInsert`

## [0.22.0]
- `ilFavouritesDBRepository`

## [0.21.0]
- Remove ILIAS 5.3 support
- Deprecate `self::dic()->tree()` (> `self::dic()->repositoryTree()`)

## [0.20.5]
- Fix ILIAS 6

## [0.20.4]
- Fix

## [0.20.3]
- `rbac` changes

## [0.20.2]
- Auto replace manually line breaks text in language files to real line breaks

## [0.20.1]
- Fix

## [0.20.0]
- Deprecate `self::dic()->mainTemplate()`
- Switch from `ilTemplate` to `Template`

## [0.19.4]
- Fix

## [0.19.3]
- Remove ILIAS 5.2 hints

## [0.19.2]
- `setLocator` on output

## [0.19.1]
- Fix

## [0.19.0]
- Add some new ILIAS 6 methods

## [0.18.5]
- Fix ILIAS 6 version

## [0.18.4]
- Add exists `self::dic()->dic()` to interface

## [0.18.3]
- ILIAS 6.0 `uiServices`

## [0.18.2]
- Supports output `ilTemplateWrapper`

## [0.18.1]
- Fix ILIAS 6.0 global template

## [0.18.0]
- Fix ILIAS 6.0 global template

## [0.17.9]
- Fix PostgreSQL recall `createAutoIncrement`
- Update readme

## [0.17.8]
- `Database::store`

## [0.17.7]
- `Database::fetchObjectCallable`

## [0.17.6]
- `Database::fetchObjectClass`

## [0.17.5]
- DatabaseDetector readme

## [0.17.4]
- DatabaseDetector readme

## [0.17.3]
- Fix

## [0.17.2]
- Fix

## [0.17.1]
- Supports PostgreSQL

## [0.17.0]
- Custom DatabaseDetector

## [0.16.1]
- Fix `self::dic()->log()` is an instance of `ilComponentLogger`

## [0.16.0]
- PHP7 syntax
- Min. ILIAS 5.3

## [0.15.6]
- Fix LoggingServices exists in ILIAS 5.2
- Pass `$DIC` by reference to prevent `clearCache`, if `$DIC` should be replaced somewhere in ILIAS core ...

## [0.15.4]
- Fix

## [0.15.3]
- Allow plugins to modify library languages if needed

## [0.15.2]
- LibraryLanguageInstaller

## [0.15.1]
- LibraryLanguageInstaller

## [0.15.0]
- LibraryLanguageInstaller
- Changed `setPlugin` to `withPlugin` in `Pluginable`

## [0.14.13]
- Add GlobalScreen for ILIAS 5.4

## [0.14.12]
- Add AsqFactory for ILIAS 6.0

## [0.14.11]
- Fixes

## [0.14.10]
- Fix stupid broken ilTable2GUI (render has only header without rows)

## [0.14.9]
- Output getHTML change order check

## [0.14.8]
- Remove @deprecated from getPluginObject

## [0.14.7]
- Output ...

## [0.14.6]
- Output ...

## [0.14.5]
- Output $exit_after=true default

## [0.14.4]
- Output $exit_after
- DICException::CODE_X

## [0.14.3]
- Fix ILIAS 5.2

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
