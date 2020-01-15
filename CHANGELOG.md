# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog][keepachangelog] and this project adheres to [Semantic Versioning][semver].

## v3.0.0

### Changed

- **Package was totally rewrote**

### Added

- Events for interaction with worker loop:
  * `BeforeLoopStartedEvent`
  * `BeforeLoopIterationEvent`
  * `BeforeRequestHandlingEvent`
  * `AfterRequestHandlingEvent`
  * `AfterLoopIterationEvent`
  * `AfterLoopStoppedEvent`
- Listeners:
  * `BindRequestListener`
  * `ClearInstancesListener`
  * `CloneConfigListener`
  * `FixSymfonyFileValidationListener`
  * `ForceHttpsListener`
  * `InjectStatsIntoRequestListener`
  * `RebindHttpKernelListener`
  * `RebindRouterListener`
  * `RebindViewListener`
  * `ResetDbConnectionsListener`
  * `ResetProvidersListener`
  * `ResetSessionListener`
  * `RunGarbageCollectorListener`
  * `SetServerPortListener`
  * `UnqueueCookiesListener`
- `Spiral\RoadRunner\PSR7Client` instance in application containers (closes [#21])
- Environment variable `APP_REFRESH` supports
- Package configuration file
- PHP `7.4` supports
- GitHub actions for a tests running

[#21]:https://github.com/avto-dev/roadrunner-laravel/issues/21

## v2.2.0

### Changed

- Maximal `illuminate/*` packages version now is `6.*`

## v2.1.0

### Added

- Middleware `SetServerPortMiddleware` for automatic setting `SERVER_PORT` in server parameters bag if it does not set before (value based on request schema; this middleware fixes **empty port value** like `https://127.0.0.1:/` when exposed default port without set `SERVER_PORT`)
- Environment variable `RR_WORKER_CLASS` supports for overriding default worker class (watch in `./bin/rr-worker`)
- Docker-based environment for development
- Project `Makefile`

### Changed

- Allowed RR configuration options (`http.fcgi.*`, `http.http2.*`, `headers.*`) (**do not forget update your existing config files**)
- Minimal `Laravel` version now is `5.5.x`
- Minimal `spiral/roadrunner` version now is `^1.4.6`
- Composer scripts
- Package service-provider automatically register `SetServerPortMiddleware` middleware
- Constant `RULE_METHOD_PREFIX` in `CallbacksInitializer` class now protected
- Constants `BOOL_OPTION_INVERT_LOGIC_NAME_PREFIX` and `OPTIONS_PREFIX` in `StartOptions` class now protected
- Method `start()` must returns `void` in `WorkerInterface` interface

### Removed

- Dev-dependency `avto-dev/dev-tools`

## v2.0.0

### Added

- Laravel **v5.8** supports

### Changed

- Minimal `php` version now is `7.1.3`
- Minimal `illuminate/*` and `laravel/*` package versions now is `>=5.8`

## v1.4.0

### Changed

- Minimal `spiral/roadrunner` version now is `^1.4`

### Added

- RR Config option `trustedSubnets`
- RR Config option `limit`

### Deprecated

- RR Config option `maxRequest` has been deprecated in favor of `maxRequestSize`

## v1.3.0

### Fixed

- Files upload mechanism (Symfony file validation bug) [RR#133], [#10]

### Added

- Worker option `--not-fix-symfony-file-validation` for disabling upload mechanism fix

[RR#133]:https://github.com/spiral/roadrunner/issues/133
[#10]:https://github.com/avto-dev/roadrunner-laravel/issues/10

## v1.2.1

### Fixed

- Middleware `ForceHttpsMiddleware` now set `HTTPS` server parameter to `on` _(required for correct working request methods like ::isSecure and others)_

## v1.2.0

### Added

- Worker option `--(not-)inject-stats-into-request` for injecting macroses into request object for accessing timestamp and allocated memory size (before request processing) values

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
