<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Zend\Config\Config;
use Zend\Config\Factory;
use Zend\Config\Processor;
use Zend\ServiceManager\ServiceManager;
use Zend\Config\Reader\Yaml as YamlReader;
use Zend\Config\Reader\Json as JsonReader;

use Zend\ConfigAggregator\ArrayProvider;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;
use Zend\ConfigAggregator\ZendConfigProvider;

use Obullo\Config\ConfigLoader;

class ConfigLoaderTest extends TestCase
{
	public function setUp()
	{
		if (! defined('CONFIG_CACHE_FILE')) {
			define('CONFIG_CACHE_FILE', ROOT.'/tests/var/cache/config.php');
		}
		$container = new ServiceManager;
		$container->setService('yaml', new YamlReader([SymfonyYaml::class, 'parse']));

		Factory::registerReader('yaml', $container->get('yaml'));
		Factory::setReaderPluginManager($container);
	}

	public function testLoadYamlFile()
	{
		$loader = new ConfigLoader(
			['config_cache_enabled' => false],
			CONFIG_CACHE_FILE
		);
		$config = $loader->load(ROOT, '/tests/var/config/framework.yaml')
			->toArray()['framework'];

    	$this->assertArrayHasKey('cookie', $config);
    	$this->assertArrayHasKey('domain', $config['cookie']);
    	$this->assertArrayHasKey('path', $config['cookie']);
    	$this->assertArrayHasKey('secure', $config['cookie']);
    	$this->assertArrayHasKey('httpOnly', $config['cookie']);
    	$this->assertArrayHasKey('expire', $config['cookie']);
    	$this->assertArrayHasKey('name', $config['session']);
    	$this->assertEquals('sessions', $config['session']['name']);
	}

	public function testLoaderConfigAsObject()
	{
		$loader = new ConfigLoader(
			['config_cache_enabled' => false],
			CONFIG_CACHE_FILE
		);
		$config = $loader->load(ROOT, '/tests/var/config/framework.yaml')
			->framework;

    	$this->assertEquals('en', $config->translator->default_locale);
    	$this->assertEquals('sessions', $config->session->name);
	}

	public function testLoadJsonFile()
	{
		$container = new ServiceManager;
		$container->setService('json', new JsonReader());

		Factory::registerReader('json', $container->get('json'));
		Factory::setReaderPluginManager($container);

		$loader = new ConfigLoader(
			['config_cache_enabled' => false],
			CONFIG_CACHE_FILE
		);
		$config = $loader->load(ROOT, '/tests/var/config/framework.json')
			->toArray()['framework'];

    	$this->assertArrayHasKey('cookie', $config);
    	$this->assertArrayHasKey('domain', $config['cookie']);
    	$this->assertArrayHasKey('path', $config['cookie']);
    	$this->assertArrayHasKey('secure', $config['cookie']);
    	$this->assertArrayHasKey('httpOnly', $config['cookie']);
    	$this->assertArrayHasKey('expire', $config['cookie']);
    	$this->assertArrayHasKey('name', $config['session']);
    	$this->assertEquals('sessions', $config['session']['name']);
	}

	public function testLoadCachedFile()
	{
		$loader = new ConfigLoader(
			['config_cache_enabled' => true],
			CONFIG_CACHE_FILE
		);
		if (file_exists(ROOT.'/tests/var/cache/config.php')) {
			unlink(ROOT.'/tests/var/cache/config.php');
		}
		$loader->load(ROOT, '/tests/var/config/amqp.yaml')
			->toArray()['amqp'];

		$data = require ROOT.'/tests/var/cache/config.php';
		$config = $data['LOCAL']['amqp']['amqp'];

    	$this->assertArrayHasKey('host', $config);
    	$this->assertEquals($config['host'], '127.0.0.1');
	}

    public function testLoadEnvConfigFile()
    {
		$loader = new ConfigLoader(
			['config_cache_enabled' => true],
			CONFIG_CACHE_FILE
		);
		$loader->setEnv('dev');
        $data = $loader->load(ROOT, '/tests/var/config/%s/framework.yaml')
        	->toArray()['framework'];

        $this->assertArrayHasKey('cookie', $data);
        $this->assertArrayHasKey('domain', $data['cookie']);
        $this->assertArrayHasKey('path', $data['cookie']);
        $this->assertArrayHasKey('secure', $data['cookie']);
        $this->assertArrayHasKey('httpOnly', $data['cookie']);
        $this->assertArrayHasKey('expire', $data['cookie']);
        $this->assertArrayHasKey('name', $data['session']);
        $this->assertEquals('sessions', $data['session']['name']);
    }
}