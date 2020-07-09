# Changelog

## [0.19.6]
- Fix

## [0.19.5]
- Fix

## [0.19.4]
- Fix

## [0.19.3]
- Fix

## [0.19.2]
- Fix

## [0.19.1]
- Fix

## [0.19.0]
- Refactor

## [0.18.7]
- Fix

## [0.18.6]
- Fix

## [0.18.5]
- Fix

## [0.18.4]
- Fix

## [0.18.3]
- Fix

## [0.18.2]
- Fix

## [0.18.1]
- Fix

## [0.18.0]
- Refactor, deprecated old `ActiveRecordConfig`
- Min PHP 7.0

## [0.17.1]
- Remove ILIAS 5.2 hints

## [0.17.0]
- Deprecated `ActiveRecordConfigGUI`

## [0.16.4]
- Add plugin to locator

## [0.16.3]
- Fix redirect after process table filter

## [0.16.2]
- Fix

## [0.16.1]
- Fix

## [0.16.0]
- `addTab` overridable

## [0.15.1]
- Fix reset offset

## [0.15.0]
- Supports an custom command or other gui class as tab

## [0.14.3]
- Fix initFilterFields

## [0.14.2]
- Fixes

## [0.14.1]
- ActiveRecordObjectFormGUI

## [0.14.0]
- ActiveRecordObjectFormGUI

## [0.13.4]
- Use ConfigPropertyFormGUI

## [0.13.3]
- ActiveRecordConfigException::CODE_X

## [0.13.2]
- Some improvments in PropertyFormGUI

## [0.13.1]
- New OutputInterface

## [0.13.0]
- TableGUI

## [0.12.2]
- BaseTableGUI

## [0.12.1]
- BaseTableGUI

## [0.12.0]
- BaseTableGUI

## [0.11.6]
- Fix

## [0.11.5]
- Fix

## [0.11.4]
- Fix

## [0.11.3]
- Fix

## [0.11.2]
- Move PropertyFormGUI to CustomInputGUIs

## [0.11.1]
- Fix and improvment PropertyForm

## [0.11.0]
- New PropertyForm

## [0.10.0]
- getDefaultValue

## [0.9.4]
- Fix on some strange PHP versions

## [0.9.3]
- Fixes

## [0.9.2]
- Add missing return in `setField`

## [0.9.1]
- Easier: Default value is now voluntary

## [0.9.0]
- Rewrite ActiveRecordConfig for supports easier handling fields: Define your fields in the `$fields` variable and access by `getField`, `setField` and `removeField
- DateTime datatype

## [0.8.0]
- getCmdForTab and redirectToTab

## [0.7.5]
- Fix boolean datatype

## [0.7.4]
- Fix

## [0.7.3]
- Fix

## [0.7.2]
- Fix

## [0.7.1]
- Fix

## [0.7.0]
- Correctly store boolean
- Rename setForm to initForm
- LANG_MODULE_CONFIG
- Multiple config tabs support
- Config table gui support

## [0.6.4]
- Update readme

## [0.6.3]
- Update readme

## [0.6.2]
- Fix create `ActiveRecordConfigFormGUI` instance again

## [0.6.1]
- Fix create `ActiveRecordConfigFormGUI` instance

## [0.6.0]
- Base `ActiveRecordConfigGUI` and `ActiveRecordConfigFormGUI`

## [0.5.14]
- Added dependencies to readme

## [0.5.13]
- Add `@author` to classes

## [0.5.12]
- Update readme

## [0.5.11]
- PHP7 comments

## [0.5.10]
- PHP7 comments

## [0.5.9]
- Fix

## [0.5.8]
- Rename `deleteName` to `removeName`

## [0.5.7]
- Use always latest version of ActiveRecordConfig

## [0.5.6]
- Use latest DIC

## [0.5.5]
- Use latest DIC

## [0.5.4]
- DICStatic

## [0.5.3]
- Use latest DIC

## [0.5.2]
- Adjustment suggestions

## [0.5.1]
- Updated Readme

## [0.5.0]
- Updated PHPDoc
- Make all methods `protected` and rename some
  - `getAll` to `getValues`
  - `setAll` to `setValues`
  - `deleteConfig` to `deleteName`
- Updated Readme

## [0.4.7]
- Removed `il` prefix from examples

## [0.4.6]
- Require `ActiveRecord` hint

## [0.4.5]
- PSR-4

## [0.4.4]
- New DICTrait version

## [0.4.3]
- Fix json default value

## [0.4.2]
- Second example update step in README.md

## [0.4.1]
- Second example update step in README.md

## [0.4.0]
- Supports default values for other types
- Rename `Date` to `Timestamp`
- Added datatypes to README.md

## [0.3.0]
- Supports default values for strings

## [0.2.0]
- Rename `key` column to `name` because bug in ILIAS `ActiveRecord` (Not escape Keys in `WHERE`)

## [0.1.0]
- First version
