
# Obullo / Config

[![Build Status](https://travis-ci.org/obullo/Config.svg?branch=master)](https://travis-ci.org/obullo/Config)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)

> Uygulamanızdaki konfigürasyon dosyalarını okuyarak konfigürasyon yönetimi üstlenen bağımsız bir pakettir.

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

Örnel bir .yaml konfigürasyon dosyası.

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

Uygulama konfigürasyonu

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

Konfigürasyon dosyası

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

## Önbellekleme

Yüklenen her bir dosya belirtilen önbellekleme işleyicisi ile önbelleğe alınır ve değişiklik yaptığınızda bu önbellek tazelenir. Böylece her defasında bu dosyalar için çözümleme işlemi yapılmamış olur.

#### Önbellek sınıfları

* FileHandler
* MemcachedHandler
* RedisHandler

Varsayılan sürücü `FileHandler` sınıfıdır. Eğer önbellek işleyici FileHandler olarak ayarlanmışsa yazma işlemlerinin yürütülebilmesi için belirtilen dizine yazma izni verilmesi gerekir.

## Ortam Değişkeni

Bir klasör yolu içerisinde '%s' değişkeni kullandığınızda bu değişken `APP_ENV` değeri ile değiştirilir.

```
/config/%s/amqp.yaml
/config/dev/amqp.yaml  // after replacement
```

Ortam değişkeni `setEnv` metodu ile ayarlanabilir.

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

## getenv() fonksiyonu

Dosya içerisindeki tanımlı olan '%env()%' fonksiyonları için her defasında doğal php `getenv()` metodu çalıştırılır.

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

> Env değişkenleri atamak için `putenv('VARIABLE=VALUE')` metodunu yada bunun için daha kapsamlı olan <a href="https://packagist.org/packages/vlucas/phpdotenv">vlucas/phpdotenv</a> paketini kullanabilirsiniz.


## Konfigurasyon değişkenleri

Aşağıdaki gibi bazı durumlarda bir konfigürasyon dosyası içerisinde dinamik değişkenler kullanmanız gerekir.

```php
# cache
# 

dir: '%root%/var/cache/'
```

Bu durumda `addVariable()` metodu ile okuyucu sınıfınıza bu değişkenleri önceden tanımlanız gerekir.


```php
$reader = new YamlReader($cacheHandler);
$reader->addVariable('%root%', ROOT);
$reader->addVariable('%foo%','bar');
```

## Dosyaları tek tek yüklemek

Örnek bir .yaml dosyası

```
# routes
#

home:
    method: GET
    path: /
    handler: App\Controller\DefaultController::index
```

Dizi türünde okuma

```php
$routes = $loader->load(ROOT, '/config/routes.yaml');

echo $routes['home']->method; // GET
echo $routes['home']->path; // "/""
echo $routes['home']->handler; // App\Controller\DefaultController::index
```

Nesne türüne dönüştürme için ikinci parametreden `true` değeri göndermeniz gerekir.

```php
$routes = $loader->load(ROOT, '/config/routes.yaml', true)

echo $routes->home->method; // GET
echo $routes->home->path; // "/""
echo $routes->home->handler; // App\Controller\DefaultController::index
```

## Dosyaları bir kerede yükleme

Eğer uygulama başlatıldığında bazı konfigürasyon dosyalarını toplu olarak yüklemeniz gerekiyor ise tek tek yüklemek yerine bunun için aşağıdaki yöntemi izleyebilirsiniz.

```php
$loader = new Loader;
$loader->setEnv(getenv('APP_ENV'));
$loader->registerReader('yaml', new YamlReader($cacheHandler));

// Put all config files here you want to load at bootstrap.

$config = $loader->loadFiles(
    [
        ROOT.'/config/%s/framework.yaml',
        ROOT.'/config/%s/database.yaml',
    ],
    true
);
```