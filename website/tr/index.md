
# Obullo / Config

[![Build Status](https://travis-ci.org/obullo/Config.svg?branch=master)](https://travis-ci.org/obullo/Config)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/obullo/config.svg)](https://packagist.org/packages/obullo/config)

> Konfigürasyon yükleyici, ortam dosyaları desteği ile gelen `zend/config`  paketi üzerine geliştirilmiş bir pakettir.

## Yükleme

``` bash
$ composer require obullo/config
```

## Minumum gereksinim

Bu versiyon php dilinin aşağıdaki sürümlerini destekler.

* 7.0
* 7.1
* 7.2

## Test etme

``` bash
$ vendor/bin/phpunit
```

## Başlangıç

Küresel konfigürasyon

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

Küresel konfigürasyon nesnesi

```php
$container->setService('config', new Config($config, true));  
```

Yerel konfigürasyon yükleyicisi

```php
use Obullo\Config\ConfigLoader;

$loader = new ConfigLoader(
    $config,
    CONFIG_CACHE_FILE
);
$container->setService('loader', $loader);
```

### Küresel dosyaları okumak

```php
$container->get('config')->foo->bar; // value
```

### Yerel dosyaları okumak

```php
$amqp = $container->get('loader')
        ->load(ROOT, '/config/amqp.yaml')
        ->amqp;

echo $amqp->host; // 127.0.0.1
```

### Okuyucular

Eğer yeni bir okuyucu eklemek istersek bunu en tepede ilan etmemiz gerekir.

```php
$container = new ServiceManager;
$container->setService('json', new JsonReader);
$container->setService('yaml', new YamlReader([SymfonyYaml::class, 'parse']));

Factory::registerReader('json', $container->get('json'));
Factory::registerReader('yaml', $container->get('yaml'));
Factory::setReaderPluginManager($container);
```

Json dosyalarını okumak

```php
$amqp = $container->get('loader')
        ->load(ROOT, '/config/amqp.json')
        ->amqp;

echo $amqp->host; // 127.0.0.1
```

Php dosyaları için herhangi bir tanımlamaya gerek duyulmaz.

```php
$amqp = $container->get('loader')
        ->load(ROOT, '/config/amqp.php')
        ->amqp;

echo $amqp->host; // 127.0.0.1
```

## Ortam değişkeni

Örnek bir .yaml konfigürasyon dosyası.

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

Örnek ortam değişkenlerini dolduralım.

```php
putenv('AMQP_USERNAME', 'guest');
putenv('AMQP_PASSWORD', 'guest');
```

Env değerlerini okumak için env işleyicisini çağırıyoruz.

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

Eğer dosya yolu içerisinde '%s' değeri kullanırsak bu değişken 'APP_ENV' değeri ile değiştirilir.


```
/config/%s/amqp.yaml
/config/dev/amqp.yaml  // after replacement
```

Ortam değişkeni `setEnv` metodu ile atanabilir.

```php
$loader = $container->get('loader');
$loader->setEnv(getenv('APP_ENV'));
$loader->addProcessor(new EnvProcessor);

$amqp = $loader->load(ROOT, '/config/%s/amqp.yaml')
        ->amqp;

echo $amqp->password;  // guest
```

## getenv() fonksiyonu

Dosya içerisindeki tanımlı olan 'env()' fonksiyonları için her defasında doğal php getenv() metodu çalıştırılır.

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

> Env değişkenleri atamak için putenv('VARIABLE=VALUE') metodunu yada bunun için daha kapsamlı olan <a href="https://packagist.org/packages/vlucas/phpdotenv">vlucas/phpdotenv</a> paketini kullanabilirsiniz.


## Constant işleyicisi

Aşağıdaki gibi bazı durumlarda bir konfigürasyon dosyası içerisinde php sabitlerini kullanmanız gerekebilir.

```php
# cache
# 

dir: 'ROOT/var/cache/'
```

Bu durumda aşağıdaki gibi Constant işleyicisini kullanmanız gereklidir. 

```php
use Zend\Config\Processor\Constant as ConstantProcessor;

$loader = $container->get('loader');
$loader->setEnv(getenv('APP_ENV'));
$loader->addProcessor(new ConstantProcessor);

$cache = $loader->load(ROOT, '/config/%s/cache.yaml')
        ->cache;

echo ROOT; // /var/www/myproject
echo $cache->dir; // /var/www/myproject/var/cache/
```

## Dizi türüne dönüştürme

```php
$amqp = $loader->load(ROOT, '/config/%s/amqp.yaml')
        ->toArray()['amqp'];

echo $amqp['host']; // 127.0.0.1
```
