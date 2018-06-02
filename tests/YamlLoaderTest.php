<?php

use Obullo\Config\Reader\YamlReader;
use Obullo\Config\Cache\FileHandler;
use Obullo\Config\Loader;

class YamlLoaderTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->filename = ROOT.'/tests/var/config/framework.yaml';
        $fileHandler = new FileHandler('/tests/var/cache/config/');
        
        $reader = new YamlReader($fileHandler);

        $this->loader = new Loader;
        $this->loader->registerReader('yaml', $reader);
    }

    public function testLoad()
    {
    	$data = $this->loader->load(ROOT, '/tests/var/config/framework.yaml')['framework'];

    	$this->assertArrayHasKey('cookie', $data);
    	$this->assertArrayHasKey('domain', $data['cookie']);
    	$this->assertArrayHasKey('path', $data['cookie']);
    	$this->assertArrayHasKey('secure', $data['cookie']);
    	$this->assertArrayHasKey('httpOnly', $data['cookie']);
    	$this->assertArrayHasKey('expire', $data['cookie']);
    	$this->assertArrayHasKey('name', $data['session']);
    	$this->assertEquals('sessions', $data['session']['name']);
    }

    public function testLoadFiles()
    {
        $data = $this->loader->loadFiles(
            [
                ROOT.'/tests/var/config/framework.yaml',
                ROOT.'/tests/var/config/routes.yaml',
            ]
        );
        $this->assertArrayHasKey('cookie', $data['framework']);
        $this->assertArrayHasKey('domain', $data['framework']['cookie']);
        $this->assertArrayHasKey('home', $data);
        $this->assertArrayHasKey('test', $data);
        $this->assertEquals('/', $data['home']['path']);
        $this->assertEquals('sessions', $data['framework']['session']['name']);
    }

    public function testLoadEnvConfigFile()
    {
        $data = $this->loader->load(ROOT, '/tests/var/config/%s/framework.yaml')['framework'];

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