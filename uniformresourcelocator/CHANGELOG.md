# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [4.5.0]
- Fix path issue on Windows
- Added new `Normalizer::normalize` static class to help with path normalization. All paths are now transmormed to `/` to make comparison easier when OS pass path contains `\` or `C:\`.
- Reworked `Resource` class : 
  - Added `setPath`, `setLocation` & `setStream` methods
  - Removed `getSeparator` & `setSeparator`
- Removed `setLocatorBasePath`, `getSeparator` and `setSeparator` from `ResourceInterface` 
- Removed `normalize` from `ResourceLocator`. Replaced by `Normalizer::normalize`
- Added type hint across code

## [4.4.3]
- Replaced Travis with GitHub Action for build
- Upgrade deprecation in tests
- Provides better exception message when scheme doesn't exist

## [4.4.2]
- Fix issue where extra `/` would be present in the calculated path for stream with empty ('') path ([#16]).
- Update PHP7 type hinting & DocBloc

## [4.4.1]
- Fix RocketTheme integration

## [4.4.0]
- Version bump

## [4.3.3]
- Added PHP 7.4 test to Travis
- Added `stream_set_option` to support PHP 7.4

## [4.3.2]
- Added `ResourceLocator::registerSharedStream` method

## [4.3.1]
- Added `__tostring` to ResourceInterface
- Added `__invoke` to ResourceLocatorInterface
- `ResourceLocationInterface::setPath` don't accept a `null` value anymore (produced an error anyway)
- Added proper PHP7 type hints
- Misc code quality and docblock fix

## [4.3.0]
- Dropping support for PHP 5.6 & 7.0
- Updated rockettheme/toolbox to 1.4.x
- Updated Laravel Illuminate packages to 5.8
- Updated PHPUnit to 7.5

## [4.2.3]
 - Added `sort` param to `listResources` method [#4]

## [4.2.2]
 - Normalize base path to fix Windows paths separator issue
 - Added AppVeyor config for Windows based CI

## [4.2.1]
 - Added `ResourceInterface`, `ResourceLocationInterface`, `ResourceLocatorInterface` & `ResourceStreamInterface`

## 4.2.0
 - Initial Release

<!--
## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed

### Security
-->

[4.4.3]: https://github.com/userfrosting/uniformresourcelocator/compare/4.4.3...4.5.0
[4.4.3]: https://github.com/userfrosting/uniformresourcelocator/compare/4.4.2...4.4.3
[4.4.2]: https://github.com/userfrosting/uniformresourcelocator/compare/4.4.1...4.4.2
[4.4.1]: https://github.com/userfrosting/uniformresourcelocator/compare/4.4.0...4.4.1
[4.4.0]: https://github.com/userfrosting/uniformresourcelocator/compare/4.3.3...4.4.0
[4.3.3]: https://github.com/userfrosting/uniformresourcelocator/compare/4.3.2...4.3.3
[4.3.2]: https://github.com/userfrosting/uniformresourcelocator/compare/4.3.1...4.3.2
[4.3.1]: https://github.com/userfrosting/uniformresourcelocator/compare/4.3.0...4.3.1
[4.3.0]: https://github.com/userfrosting/uniformresourcelocator/compare/4.2.3...4.3.0
[4.2.3]: https://github.com/userfrosting/uniformresourcelocator/compare/4.2.2...4.2.3
[4.2.2]: https://github.com/userfrosting/uniformresourcelocator/compare/4.2.1...4.2.2
[4.2.1]: https://github.com/userfrosting/uniformresourcelocator/compare/4.2.0...4.2.1
[#4]: https://github.com/userfrosting/UniformResourceLocator/issues/4
[#16]: https://github.com/userfrosting/UniformResourceLocator/issues/16
