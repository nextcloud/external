# Changelog
All notable changes to this project will be documented in this file.

## 5.2.0 – 2023-05-16
### Changed
- Require Nextcloud 27

## 5.1.0 – 2023-02-15
### Changed
- Require Nextcloud 26

### Fixed
- Fix dark/bright mode icon for the default settings icon

## 5.0.1 – 2023-02-15
### Fixed
- Fix dark/bright mode icon for the default settings icon
  [#412](https://github.com/nextcloud/external/pull/412)

## 5.0.0 – 2022-10-18
### Added
- Added support for JWT [See documentation](https://github.com/nextcloud/external/blob/master/docs/jwt-sample.php)

## 4.1.0 – 2022-09-14
### Fixed
- Use rawurlencode to encode the URL parameters
  [#298](https://github.com/nextcloud/external/pull/298)
- Require Nextcloud 25

## 4.0.0 – 2022-04-07
### Fixed
- Accessibility Issue: Inline frames must have a unique, non-empty 'title' attribute
  [#287](https://github.com/nextcloud/external/pull/287)
- Require Nextcloud 24

## 3.10.2 – 2021-11-09
### Fixed
- Compatibility with Nextcloud 23

## 3.9.0 – 2021-06-15
### Fixed
- Accessibility Issue: Inline frames must have a unique, non-empty 'title' attribute
  [#257](https://github.com/nextcloud/external/pull/257)
- Use prepared list of pages to redirect to the page
  [#244](https://github.com/nextcloud/external/pull/244)
- urlencode the parameters
  [#243](https://github.com/nextcloud/external/pull/243)
- Compatibility with Nextcloud 22

## 3.8.2 – 2021-06-15
### Fixed
- Accessibility Issue: Inline frames must have a unique, non-empty 'title' attribute
  [#259](https://github.com/nextcloud/external/pull/259)
- Use prepared list of pages to redirect to the page
  [#245](https://github.com/nextcloud/external/pull/245)
- urlencode the parameters
  [#248](https://github.com/nextcloud/external/pull/248)

## 3.7.3 – 2021-06-15
### Fixed
- Accessibility Issue: Inline frames must have a unique, non-empty 'title' attribute
  [#260](https://github.com/nextcloud/external/pull/260)
- Use prepared list of pages to redirect to the page
  [#246](https://github.com/nextcloud/external/pull/246)
- urlencode the parameters
  [#249](https://github.com/nextcloud/external/pull/249)

## 3.8.1 – 2021-01-25
### Fixed
- Fix "Fileupload not a function on chrome"

## 3.8.0 – 2020-12-15
### Fixed
- Compatibility with Nextcloud 21

## 3.7.2 – 2021-01-25
### Fixed
- Fix "Fileupload not a function on chrome"

## 3.7.1 – 2020-10-07
### Fixed
 - Fix missing compiled JS

## 3.7.0 – 2020-09-04
### Fixed
 - Compatibility with Nextcloud 20

## 3.6.0 – 2020-06-03
### Fixed
 - Compatibility with Nextcloud 19

## 3.5.0 – 2020-01-17
### Fixed
 - Compatibility with Nextcloud 18

## 3.4.1 – 2019-10-15
### Fixed
 - Make sure the white icon is also shown in 32px width
  [#161](https://github.com/nextcloud/external/pull/161)
 - Add Content-Security-Policy to allow inline attributes on SVG icons
  [#157](https://github.com/nextcloud/external/pull/157)
 - Resize the iframe while it is still loading
  [#154](https://github.com/nextcloud/external/pull/154)
 - Allow to fullscreen the embeded content
  [#151](https://github.com/nextcloud/external/pull/151)

## 3.4.0 – 2019-09-03
### Fixed
 - Compatibility with Nextcloud 17

## 3.3.0 – 2019-03-29
### Added
 - Allow to add links to the login page
  [#111](https://github.com/nextcloud/external/pull/111)

### Fixed
 - Compatibility with Nextcloud 16

## 3.2.0 – 2018-11-16
### Fixed
 - Compatibility with Nextcloud 15

## 3.1.0 – 2018-08-02
### Fixed
 - Compatibility with Nextcloud 14

## 3.0.4 – 2018-08-10
### Fixed
 - Fix personal settings after removing a quota link [#108](https://github.com/nextcloud/external/pull/108)

## 3.0.3 – 2018-05-09
### Fixed
 - Bring back the quota link on the personal page
  [#94](https://github.com/nextcloud/external/pull/94)

## 3.0.2 – 2018-02-07
### Fixed
 - Fix placeholders not working in the browser
  [#73](https://github.com/nextcloud/external/pull/73)

## 3.0.1 – 2018-01-10
### Added
 - Allow to add email, user id and displayname as placeholders in URLs
  [#66](https://github.com/nextcloud/external/pull/66)

## 3.0.0 – 2017-11-16
### Added
 - Support for Nextcloud 13
 - Allow to add sites for members of a group
  [#44](https://github.com/nextcloud/external/pull/44)
 - Option to upload icons in the admin settings
  [#46](https://github.com/nextcloud/external/pull/46)
 - Allow to redirect to sites when they can not be embedded
  [#43](https://github.com/nextcloud/external/pull/43)

### Changed
 - Allow to set the app as default app
  [#51](https://github.com/nextcloud/external/pull/51)

### Fixed
 - Also display "Quota" link on the sidebar of the files app
  [#40](https://github.com/nextcloud/external/pull/40)
 - No more integrity warning when icons are uploaded
  [#46](https://github.com/nextcloud/external/pull/46)
