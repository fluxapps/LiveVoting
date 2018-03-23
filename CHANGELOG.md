# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [3.7.0] - 2018-03-23

### Added
- ILIAS 5.3 support
- Feature: Presenter link
- Feature: Export PowerPoint with slides for each questions with presenter link
- Feature: Presentation of number range

### Changed
-

### Removed
- Removed ILIAS 5.1 support

### Fixed
- Fix no wrap if the shortlink URL is too long
- Fix no preventDefault on fullscreen button which caused unwanted redirects
- Fixed filtering by participant for anonymous participants

### Improved
- Refactoring

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
