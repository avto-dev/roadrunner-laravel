<p align="center">
  <img src="https://laravel.com/assets/img/components/logo-laravel.svg" alt="Laravel" width="240" />
</p>

# [RoadRunner][roadrunner] ⇆ Laravel bridge

[![Version][badge_packagist_version]][link_packagist]
[![Version][badge_php_version]][link_packagist]
[![Build Status][badge_build_status]][link_build_status]
[![Coverage][badge_coverage]][link_coverage]
[![Code quality][badge_code_quality]][link_code_quality]
[![Downloads count][badge_downloads_count]][link_packagist]
[![License][badge_license]][link_license]

Easy way for connecting [RoadRunner][roadrunner] and Laravel applications.

## Install

Require this package with composer using the following command:

```shell
$ composer require avto-dev/roadrunner-laravel "^1.2"
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

Данный пакет поставляет готовый к использованию воркер для RoadRunner, который вы можете расширить по собственному усмотрению - по умолчанию он наделен тем функционалом, который нам показался наиболее востребованным.

Из коробки он поддерживает следующие параметры запуска:

Имя параметра | Описание
------------- | --------
`--(not-)force-https` | Форсирует (или нет) использование схемы `https` для генерации внутренних ссылок приложения
`--(not-)reset-db-connections` | Обрывает (или нет) соединения с БД после обработки входящего запроса
`--(not-)reset-redis-connections` | Обрывает (или нет) соединения с redis после обработки входящего запроса
`--(not-)refresh-app` | Принудительно пересоздает инстанс приложения после обработки **каждого** запроса
`--(not-)inject-stats-into-request` | **Перед** обработкой **каждого** запроса добавляет в объект `Request` макросы (`::getTimestamp()` и `::getAllocatedMemory()`), возвращающие значения временной метки и объем выделенной памяти

> Параметры запуска указываются в файле-конфигурации (например: `./.rr.local.yml`) по пути `http.workers.command`, например: `php ./vendor/bin/rr-worker --some-parameter`

Так же доступны для взаимодействия следующие переменные окружения:

Имя переменной окружения | Описание
------------------------ | --------
`APP_BASE_PATH` | Путь к директории с приложением
`APP_BOOTSTRAP_PATH` | Путь к bootstrap файлу приложения _(по умолчанию `/bootstrap/app.php`)_
`APP_FORCE_HTTPS` | Форсирует использование схемы `https` для генерации внутренних ссылок приложения

### Дополнительные HTTP-заголовки 

Для форсирования `https` схемы для генерации внутренних ссылок приложения вы ты же можете использовать специальный HTTP заголовок (выставляя его, например, на реверс-прокси стоящим перед приложением) - `FORCE-HTTPS` с произвольным не пустым значением.

### Расширение функционала

Данный пакет спроектирован с учетом возможности расширения практически любых его компонентов. За всеми подробностями - "Look into the sources, Luke!".

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
[badge_code_quality]:https://img.shields.io/scrutinizer/g/avto-dev/roadrunner-laravel.svg?maxAge=180
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
[link_code_quality]:https://scrutinizer-ci.com/g/avto-dev/roadrunner-laravel/
[link_issues]:https://github.com/avto-dev/roadrunner-laravel/issues
[link_create_issue]:https://github.com/avto-dev/roadrunner-laravel/issues/new/choose
[link_commits]:https://github.com/avto-dev/roadrunner-laravel/commits
[link_pulls]:https://github.com/avto-dev/roadrunner-laravel/pulls
[link_license]:https://github.com/avto-dev/roadrunner-laravel/blob/master/LICENSE
[getcomposer]:https://getcomposer.org/download/
[roadrunner]:https://github.com/spiral/roadrunner
