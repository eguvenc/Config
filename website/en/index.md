
# Obullo / Config

[![Build Status](https://travis-ci.org/obullo/Config.svg?branch=master)](https://travis-ci.org/obullo/Config)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)

> It is a standalone package that assumes configuration management by reading the configuration files in your application.


## Install

``` bash
$ composer require obullo/config
```

## Requirements

The following versions of PHP are supported by this version.

* 7.0
* 7.1
* 7.2

## Testing

``` bash
$ vendor/bin/phpunit
```

## Quick start

An example .yaml configuration file.

```
# amqp
# 

amqp:
    host: 127.0.0.1
    port: 5672
    username: '%env(AMQP_USERNAME)%'
    password: '%env(AMQP_PASSWORD)%'
    vhost: /
```

Configuration

```php
require '../vendor/autoload.php';

define('ROOT', '/var/www/');
putenv('AMQP_USERNAME', 'guest');
putenv('AMQP_PASSWORD', 'guest');

use Obullo\Config\Cache\FileHandler;
use Obullo\Config\Reader\YamlReader;
use Obullo\Config\Loader;

$cacheHandler = new FileHandler('/path/to/cache/folder');
```

Reading configuration file

```php
$loader = new Loader;
$loader->registerReader('yaml', new YamlReader($cacheHandler));

$amqp = $loader->load(ROOT, '/config/amqp.yaml', true)
        ->amqp;

echo $amqp->host; // 127.0.0.1
echo $amqp->port; // 5672
echo $amqp->username;  // guest
echo $amqp->password;  // guest
echo $amqp->vhost;  // "/"
```

## Caching

Each uploaded file is cached with the specified cache handler and this cache is refreshed when you make changes. Thus, no parsing is done for these files every time.

#### Cache handlers

* FileHandler
* MemcachedHandler
* RedisHandler

The default cache handler is `FileHandler` class. If the cache handler is FileHandler, you need to set write permission to write to the specified directory.

## Environment variable

If you use '%s' in a  folder path, this variable is changed with the value 'APP_ENV'.

```
/config/%s/amqp.yaml
/config/dev/amqp.yaml  // after replacement
```

The environment variable can be set with the `setEnv` method.

```php
$loader = new Loader;
$loader->setEnv(getenv('APP_ENV'));
$loader->registerReader('yaml', new YamlReader($cacheHandler));

$amqp = $loader->load(ROOT, '/config/%s/amqp.yaml', true)
        ->amqp;

echo $amqp->host; // 127.0.0.1
echo $amqp->port; // 5672
echo $amqp->username;  // guest
echo $amqp->password;  // guest
echo $amqp->vhost;  // "/"
```

## getenv() function

Every time for the '%env()%' functions defined in the file, the natural php `getenv()` method is executed.

```
# amqp
# 

amqp:
    host: 127.0.0.1
    port: 5672
    username: '%env(AMQP_USERNAME)%'
    password: '%env(AMQP_PASSWORD)%'
    vhost: /
```

> You can use the `putenv('VARIABLE=VALUE')` method to assign Env variables, or the more comprehensive <a href="https://packagist.org/packages/vlucas/phpdotenv">vlucas/phpdotenv</a> package for this method .

## Configuration variables

In some cases it may be necessary to use dynamic variables in a configuration file as follows.

```php
# cache
# 

dir: '%root%/var/cache/'
```

In this case, you need to predefine these variables in your reader class with the `addVariable()` method.


```php
$reader = new YamlReader($cacheHandler);
$reader->addVariable('%root%', ROOT);
$reader->addVariable('%foo%','bar');
```

## Loading files

An example .yaml file.

```
# routes
#

home:
    method: GET
    path: /
    handler: App\Controller\DefaultController::index
```

Reading in array format,

```php
$routes = $loader->load(ROOT, '/config/routes.yaml');

echo $routes['home']['method']; // GET
echo $routes['home']['path']; // "/""
echo $routes['home']['handler']; // App\Controller\DefaultController::index
```

For object type you need to send `true` as the second parameter.

```php
$routes = $loader->load(ROOT, '/config/routes.yaml', true)

echo $routes->home->method; // GET
echo $routes->home->path; // "/""
echo $routes->home->handler; // App\Controller\DefaultController::index
```

## Loading multiple files 

If you need to load some configuration files collectively when the application is started, you can follow this method instead of loading them individually.

```php
$loader = new Loader;
$loader->setEnv(getenv('APP_ENV'));
$loader->registerReader('yaml', new YamlReader($cacheHandler));

// Put all config files here you want to load at bootstrap.

$config = $loader->loadFiles(
    [
        ROOT.'/config/%s/framework.yaml',
        ROOT.'/config/%s/database.yaml',
    ]
);
```