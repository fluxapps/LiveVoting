# Change Log

## [5.2.0]
- Change: ILIAS 7 compatibility
- Change: dropped ILIAS 5.4 compatibility

## [5.1.5]
- Fix: some language variables fixed/added

## [5.1.4]
- Fix: Additional RBAC Method in ilRbacReview-Mock for PIN Context - fixes compatibility problems with system notifications

## [5.1.3]
- css recompiled

## [5.1.2]
- Fix voting scroller in ILIAS 6
- Fix presenter view is empty

## [5.1.1]
- Fix Freitextfrage

## [5.1.0]
- Pass `lang` key in `GET` to ajax voting requests
- Remove hardcoded german language and use current

## [5.0.0]
- ILIAS 6 support
- Remove ILIAS 5.3 support

## [4.4.3]
- Fixed complication with MathJax v3

## [4.4.2]
- Fixed typos

## [4.4.1]
- Fix may event not init before mail

## [4.4.0]
- Clone questions in an other object
- Fix mail may not init

## [4.3.4]
- Security Fix: PLLV-361 - Fixed code injection in free text questions

## [4.3.3]
- Fixed Bug PLLV-345 - do not present results in more than one column
- Fixed issue with other plugins calling ilUtil::getImagePath - thx to mjansen / https://github.com/studer-raimann/LiveVoting/pull/24
- Fixed Avoid errors with devision by 0 - thx to swiniker / https://github.com/studer-raimann/LiveVoting/pull/23/
- Fix typo in function name - thx to Rillke / https://github.com/studer-raimann/LiveVoting/pull/22
- Unbreak delete-an-option thx to Rillke / 
https://github.com/studer-raimann/LiveVoting/pull/21
- Fixed Bug Uncommented method to possibly fix a bug with number range inputs - thx to rsheer / https://github.com/studer-raimann/LiveVoting/pull/18

##  [4.3.2]
- Fixed BUG SUPPORT-2161 Use of undefined constant IL_COOKIE_PATH (PHP7.2)"

## [4.3.1]
- Hotfix: Working enter pin gui again

## [4.3.0]
- feature: clustering/categorizing of free text questions 

## [4.2.0]
- ILIAS 5.4 support
- Remove ILIAS 5.2 support

## [4.1.3] - 2019-04-24
- fixed bug "Zahlen-schätzen Frage Range" PLLV-307
- fixed bug "Exceptions leiten aur error.php um, geben aber keine Fehlermeldung aus." PLLV-293

## [4.1.2] - 2019-03-26
- fixed bug "Fehler beim Zugriff auf Offline-LV" PLLV-315
- fixed bug "Powerpoint Export verhindern, wenn keine Frage vorhanden ist" PLLV-306
- Powerpoint Export hints adapted

## [4.1.1] - 2019-03-25
- fixed bug direct links for non public LiveVotings - PLLV-314
- fixed bug short links PLLV-313

## [4.1.0] - 2019-03-22

- allow copy LiveVoting objects
- rebuilt communication
- Bugfixes in PowerPoint Export
- Bugfix when saving object settings
- Other small bugfixes
- Improved PowerPoint Export

## [4.0.3] - 2018-08-02

- Removed ILIAS 5.1 support
- ILIAS 5.3 support
- Feature: Presenter link included puk
- Feature: Export PowerPoint with slides for each questions with presenter link
- Feature: Presentation of number range
- Feature: Use serif font for PIN's
- Feature: NumberRange step
- Feature: FreeInput single and multiple lines answer field
- Feature: Ask for removing data on uninstall
- Removed discard button in FreeInput
- Removed 'Colums for possible answers' in NumberRange
- Fix fullscreen background not any more black
- New behavior for random and non-random correct positions
- Parameters are not anymore stored more in cookies, but are stored now in GET parameters. So it can support a single session, like the WebViewer-AddIn in PowerPoint are used
- Full namespaces
- DIC-Trait
- CustomInputGUI's
- Fix no preventDefault on some button which caused unwanted redirects
- Fixed filtering by participant for anonymous participants
- FreeInput now don't allows empty inputs anymore
- CorrectOrder now immediately displays result without reloading page
- Fix endless loop in CorrectOrder question type with only 1 position (Only 1 position=The solution)
- Fix number range left and right buttons
- Fix new line always add on bottom in the multi line gui
- Fix visible permission
- Fix Chrome's favicon.ico (If shortlink enabled)
- Fix PegasusHelper let crash
- Refactoring
- Plugin uninstaller
- Fix no wrap if the shortlink URL is too long
- Cursor pointer for QR code button
- FreeInput enter auto submit
- Updated de and en languages and remove outdated PluginTranslator
- Screen-Id-Component
- PHP version checker
- Improved PIN validation in pin.php
- **Updates to RewriteRules might be required**
- Improved FreeInput config validation

## [4.0.2] - 2018-07-11
- Added the functionality to pin a voting to the personal workspace
- Fixed a issue which prevented the cache to work like intended in ILIAS 5.2
- Fixed a redirect loop if the page translation is enabled.
- Fixed plugin regression 
- Slightly improved ILIAS 5.2 initialisation performance
- Results filter is now working again as expected.
- The play button is now displayed as intended on small displays (presenter view).

## [3.5.0] - 2017-04-19

- New question type "Number Range" which allows user to choose a number within a range.
- Changelog
- The "Free Input" question type now groups answer duplicates together.
- The "Correct Order" question type randomizes the items for the voting participants.
- Added Semantic Versioning section to readme
- Removed some leftovers of ILIAS 5.0 compatible releases.
