<p align="center">
  <img src="https://laravel.com/assets/img/components/logo-laravel.svg" alt="Laravel" width="240" />
</p>

# [RoadRunner][roadrunner] ⇆ Laravel bridge

[![Version][badge_packagist_version]][link_packagist]
[![Version][badge_php_version]][link_packagist]
[![Build Status][badge_build_status]][link_build_status]
[![Coverage][badge_coverage]][link_coverage]
[![Downloads count][badge_downloads_count]][link_packagist]
[![License][badge_license]][link_license]

Easy way for connecting [RoadRunner][roadrunner] and Laravel applications.

## Install

Require this package with composer using the one of next commands.

For Laravel versions `5.5.x`..`5.7.x` with minimal PHP version 7.0 (version `1.x` is abandoned):

```shell
$ composer require avto-dev/roadrunner-laravel "^1.4"
```

For Laravel versions `5.5.x`..`5.8.x` with minimal PHP version 7.1.3 and above:

```shell
$ composer require avto-dev/roadrunner-laravel "^2.0"
```

> Installed `composer` is required ([how to install composer][getcomposer]).

> You need to fix the major version of package.

After that you can optionally "publish" default RoadRunner configuration files into your application root directory using next command:

```bash
$ php ./artisan vendor:publish --provider='AvtoDev\RoadRunnerLaravel\ServiceProvider' --tag=rr-config
```

If you wants to disable package service-provider auto discover, just add into your `composer.json` next lines:

```json
{
    "extra": {
        "laravel": {
            "dont-discover": [
                "avto-dev/roadrunner-laravel"
            ]
        }
    }
}
```

## Usage

This package allows you to use "production ready" worker for RoadRunner, that you can extend as you want.

Out of the box it supports next run parameters:

Name | Description
------------- | --------
`--(not-)force-https` | Force (or not) `https` schema usage (eg. for links generation)
`--(not-)reset-db-connections` | Drop (or not) database connections after incoming request serving
`--(not-)reset-redis-connections` | Drop (or not) Redis connections after incoming request serving
`--(not-)refresh-app` | Force refresh application instance after incoming request serving
`--(not-)inject-stats-into-request` | Inject into each `Request` object macros `::getTimestamp()` and `::getAllocatedMemory()` that returns timestamp and used allocated memory size
`--not-fix-symfony-file-validation` | Do **not** fix `isValid` method in `\Symfony\Component\HttpFoundation\File\UploadedFile` [#10]

> Parameters should be declared in RR configuration file (eg. `./.rr.local.yml`) in `http.workers.command`, eg. `php ./vendor/bin/rr-worker --some-parameter`

Also you can use next environment variables:

Environment name | Description
------------------------ | --------
`APP_BASE_PATH` | Base path to the application
`APP_BOOTSTRAP_PATH` | Path to the application bootstrap file _(default: `/bootstrap/app.php`)_
`APP_FORCE_HTTPS` | Force `https` schema usage (eg. for links generation)
`RR_WORKER_CLASS` | Worker class name _(default: `\AvtoDev\RoadRunnerLaravel\Worker\Worker`)_

### Additional HTTP-headers 

For forcing `https` schema usage you can pass special HTTP header `FORCE-HTTPS` with any non-empty value.

### Extending

You can extend this worker as you wish, for more information - "Look into the sources, Luke!".

### Testing

For package testing we use `phpunit` framework. Just write into your terminal _(installed `docker-ce` is required)_:

```bash
$ git clone git@github.com:avto-dev/roadrunner-laravel.git ./roadrunner-laravel && cd $_
$ make install
$ make test
```

## Changes log

[![Release date][badge_release_date]][link_releases]
[![Commits since latest release][badge_commits_since_release]][link_commits]

Changes log can be [found here][link_changes_log].

## Support

[![Issues][badge_issues]][link_issues]
[![Issues][badge_pulls]][link_pulls]

If you will find any package errors, please, [make an issue][link_create_issue] in current repository.

## License

This is open-sourced software licensed under the [MIT License][link_license].

[badge_packagist_version]:https://img.shields.io/packagist/v/avto-dev/roadrunner-laravel.svg?maxAge=180
[badge_php_version]:https://img.shields.io/packagist/php-v/avto-dev/roadrunner-laravel.svg?longCache=true
[badge_build_status]:https://travis-ci.org/avto-dev/roadrunner-laravel.svg?branch=master
[badge_coverage]:https://img.shields.io/codecov/c/github/avto-dev/roadrunner-laravel/master.svg?maxAge=60
[badge_downloads_count]:https://img.shields.io/packagist/dt/avto-dev/roadrunner-laravel.svg?maxAge=180
[badge_license]:https://img.shields.io/packagist/l/avto-dev/roadrunner-laravel.svg?longCache=true
[badge_release_date]:https://img.shields.io/github/release-date/avto-dev/roadrunner-laravel.svg?style=flat-square&maxAge=180
[badge_commits_since_release]:https://img.shields.io/github/commits-since/avto-dev/roadrunner-laravel/latest.svg?style=flat-square&maxAge=180
[badge_issues]:https://img.shields.io/github/issues/avto-dev/roadrunner-laravel.svg?style=flat-square&maxAge=180
[badge_pulls]:https://img.shields.io/github/issues-pr/avto-dev/roadrunner-laravel.svg?style=flat-square&maxAge=180
[link_releases]:https://github.com/avto-dev/roadrunner-laravel/releases
[link_packagist]:https://packagist.org/packages/avto-dev/roadrunner-laravel
[link_build_status]:https://travis-ci.org/avto-dev/roadrunner-laravel
[link_coverage]:https://codecov.io/gh/avto-dev/roadrunner-laravel/
[link_changes_log]:https://github.com/avto-dev/roadrunner-laravel/blob/master/CHANGELOG.md
[link_issues]:https://github.com/avto-dev/roadrunner-laravel/issues
[link_create_issue]:https://github.com/avto-dev/roadrunner-laravel/issues/new/choose
[link_commits]:https://github.com/avto-dev/roadrunner-laravel/commits
[link_pulls]:https://github.com/avto-dev/roadrunner-laravel/pulls
[link_license]:https://github.com/avto-dev/roadrunner-laravel/blob/master/LICENSE
[getcomposer]:https://getcomposer.org/download/
[roadrunner]:https://github.com/spiral/roadrunner
[#10]:https://github.com/avto-dev/roadrunner-laravel/issues/10
