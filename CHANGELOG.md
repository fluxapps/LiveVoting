# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [4.0.1] - 2018-08-01

### Added
- ILIAS 5.3 support
- Feature: Presenter link included puk
- Feature: Export PowerPoint with slides for each questions with presenter link
- Feature: Presentation of number range
- Feature: Use serif font for PIN's
- Feature: NumberRange step

### Changed
- Removed discard button in FreeInput
- Removed 'Colums for possible answers' in NumberRange

### Deprecated
-

### Removed
- Removed ILIAS 5.1 support

### Fixed
- Fix no preventDefault on some button which caused unwanted redirects
- Fixed filtering by participant for anonymous participants
- FreeInput now don't allows empty inputs anymore
- CorrectOrder now immediately displays result without reloading page
- Fix endless loop in CorrectOrder question type with only 1 position (Only 1 position=The solution)
- Fix number range left and right buttons
- Fix new line always add on bottom in the multi line gui
- Fix visible permission
- Fix Chrome's favicon.ico (If shortlink enabled)

### Improved
- Refactoring
- Plugin uninstaller
- Fix no wrap if the shortlink URL is too long
- Cursor pointer for QR code button
- FreeInput enter auto submit
- Updated de and en languages

### Security
- Improved PIN validation in pin.php

## [Unreleased] - XXX
### Added
- Added the functionality to pin a voting to the personal workspace
### Changed
### Deprecated
### Removed
### Fixed
- Fixed a issue which prevented the cache to work like intended in ILIAS 5.2
- Fixed a redirect loop if the page translation is enabled.
- Fixed plugin regression 
- Slightly improved ILIAS 5.2 initialisation performance
- Results filter is now working again as expected.
- The play button is now displayed as intended on small displays (presenter view).
### Security

## [3.5.0] - 2017-04-19

### Added
- New question type "Number Range" which allows user to choose a number within a range.
- Changelog

### Changed
- The "Free Input" question type now groups answer duplicates together.
- The "Correct Order" question type randomizes the items for the voting participants.
- Added Semantic Versioning section to readme

### Removed
- Removed some leftovers of ILIAS 5.0 compatible releases.
