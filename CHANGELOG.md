# Enupal Snapshot Changelog

## 1.2.8 - 2020.07.02
### Added
- Added `autosuggestField` to the Stripe Payments Order PDF Template setting

### Fixed
- Fixed issue on when setting Stripe Payments order template ([#37])

[#37]: https://github.com/enupal/snapshot/issues/37

## 1.2.7 - 2019.12.04
### Fixed
- Fixed issue for when `overrideFile` is set to false, was regenerating the file ([#35])

[#35]: https://github.com/enupal/snapshot/issues/35

## 1.2.6 - 2019.07.11
### Fixed
- Fixed "Filed to load PDF document" on some scenarios ([#29])

[#29]: https://github.com/enupal/snapshot/issues/29

## 1.2.5 - 2019.07.08
### Added
- Added support for return the asset model instead of the Url. [More info](https://enupal.com/craft-plugins/enupal-snapshot/docs/advanced/return-asset-model)

## 1.2.4 - 2019.06.25
### Added
- Added support for [environmental values](https://docs.craftcms.com/v3/config/environments.html).

## 1.2.3 - 2019.03.10
### Added
- Added support for Stripe Payments v1.8.0

## 1.2.2 - 2019.02.18
### Fixed
- Fixed `Calling unknown method: enupal\snapshot\services\Snapshots::installDefaultVolume()` when updating plugin from Craft 3.0.x

## 1.2.1 - 2019.01.25
### Updated
- Updated the Order PDF template

## 1.2.0 - 2019.01.24
### Added
- Added fully support for Craft 3.1
- Added integration with Stripe Payments to display PDF orders in templates, download PDF order in the edit order page (Control panel) and attach the PDF order to the customer email

## 1.1.2 - 2019.01.07
### Fixed
- Fixed issue where lib paths were deleted after upgrade

## 1.1.1 - 2019.01.07
### Fixed
- Fixed migration error

## 1.1.0 - 2019.01.07
### Added
- Added support to set the upload location via assets for non-inline files
- Added support to specify subpath and pass twig code
- Added the `overrideFile` setting to prevent file creation if the file already exists

## 1.0.6 - 2018.08.29
### Added
- Added timeout setting to override default 60 seconds

## 1.0.5 - 2018.08.22
### Improved
- Improved docs link in settings
- Improved validation error messages

## 1.0.4 - 2018.04.04
### Improved
- Improved code inspections

## 1.0.3 - 2018.04.03
### Fixed
- Fixed bug on settings template

## 1.0.2 - 2018.03.15
### Improved
- Improved coding-styles

## 1.0.1 - 2018.03.15
### Fixed
- Fixed deprecation errors

## 1.0.0 - 2018.01.24
### Added
- Initial release
