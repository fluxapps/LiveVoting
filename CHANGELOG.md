# Change Log

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
