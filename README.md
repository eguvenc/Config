
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

Reading config file

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

## Documentation

Documents are available at <a href="http://config.obullo.com/">http://config.obullo.com/</a>