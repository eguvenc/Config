
# Obullo / Config

[![Build Status](https://travis-ci.org/obullo/Config.svg?branch=master)](https://travis-ci.org/obullo/Config)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/obullo/config.svg)](https://packagist.org/packages/obullo/config)

> Configuration file loader built on `zend/config` package that comes with environment support.


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

Global configuration

```php
require 'vendor/autoload.php';

define('ROOT', '/var/www/myproject/');
define('CONFIG_CACHE_FILE', 'cache/config.php');

use Zend\ServiceManager\ServiceManager;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Zend\Config\Config;
use Zend\Config\Factory;
use Zend\Config\Reader\Yaml as YamlReader;

use Zend\ConfigAggregator\ArrayProvider;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\ZendConfigProvider;

$container = new ServiceManager;
$container->setService('yaml', new YamlReader([SymfonyYaml::class, 'parse']));

Factory::registerReader('yaml', $container->get('yaml'));
Factory::setReaderPluginManager($container);

$aggregator = new ConfigAggregator(
    [
        new ArrayProvider([ConfigAggregator::ENABLE_CACHE => true]),
        new ZendConfigProvider(ROOT.'config/autoload/{,*.}{json,yaml,php}'),
    ],
    CONFIG_CACHE_FILE
);
$config = $aggregator->getMergedConfig();
```

Create global config object

```php
$container->setService('config', new Config($config, true));  
```

Create local config object as loader

```php
use Obullo\Config\ConfigLoader;

$loader = new ConfigLoader(
    $config,
    CONFIG_CACHE_FILE
);
$container->setService('loader', $loader);
```

### Reading files globally

```php
$container->get('config')->foo->bar; // value
```

### Reading files locally

```php
$amqp = $container->get('loader')
        ->load(ROOT, '/config/amqp.yaml')
        ->amqp;

echo $amqp->host; // 127.0.0.1
```

## Environment variable

An example .yaml configuration file.

```
# amqp
# 

amqp:
    host: 127.0.0.1
    port: 5672
    username: 'env(AMQP_USERNAME)'
    password: 'env(AMQP_PASSWORD)'
    vhost: /
```

Fill in sample environment variables.

```php
putenv('AMQP_USERNAME', 'guest');
putenv('AMQP_PASSWORD', 'guest');
```

Add env processor to read env values.

```
use Obullo\Config\Processor\Env as EnvProcessor;
```

```php
$loader = $container->get('loader');
$loader->addProcessor(new EnvProcessor);

$amqp = $loader->load(ROOT, '/config/amqp.yaml')
        ->amqp;

echo $amqp->username;  // guest
echo $amqp->password;  // guest
```

If you use '%s' in a  folder path, this variable is changed with the value 'APP_ENV'.

```
/config/%s/amqp.yaml
/config/dev/amqp.yaml  // after replacement
```

The environment variable can be set with the `setEnv` method.

```php
$loader = $container->get('loader');
$loader->setEnv(getenv('APP_ENV'));
$loader->addProcessor(new EnvProcessor);

$amqp = $loader->load(ROOT, '/config/%s/amqp.yaml')
        ->amqp;

echo $amqp->password;  // guest
```


## Documentation

Documents are available at <a href="http://config.obullo.com/">http://config.obullo.com/</a>
