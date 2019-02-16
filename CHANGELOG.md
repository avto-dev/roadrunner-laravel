# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog][keepachangelog] and this project adheres to [Semantic Versioning][semver].

## v1.2.0

### Added

- Worker option `--(not-)update-app-stats` for updating in IoC containers timestamp and allocated memory size before each incoming request processing

## v1.1.1

### Added

- Small fix for previous feature (added `unset()` for `$kernel`)

## v1.1.0

### Added

- Supports option `--(not-)refresh-app`

## v1.0.2

### Fixed

- Methods `Worker->getDefaultAppBasePath()` and `Worker->getDefaultAppBootstrapPath()` (resolve order)

## v1.0.1

### Fixed

- Forcing https schema (using env variable) method

## v1.0.0

### Changed

- First release

[keepachangelog]:https://keepachangelog.com/en/1.0.0/
[semver]:https://semver.org/spec/v2.0.0.html
