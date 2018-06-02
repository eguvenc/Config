<?php

use Obullo\Config\Cache\FileHandler;
use Obullo\Config\Reader\YamlReader;

class YamlReaderTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->filename = ROOT.'/tests/var/config/framework.yaml';
        $this->reader = new YamlReader(new FileHandler('/tests/var/cache/config'));
    }

    public function testFromFile()
    {
    	$data = $this->reader->fromFile($this->filename);
        $this->assertEquals('sessions', $data['framework']['session']['name']);
    }

    public function testFromString()
    {
        $yaml = '
# application
#    

framework:
    cookie:
        domain:
        path: /
        secure: false
        httpOnly: true
        expire: 0
    session:
        name: sessions';
        $data = $this->reader->fromString($yaml);
        $this->assertEquals('sessions', $data['framework']['session']['name']);
    }

    public function testEnvParseRecursive()
    {
        $this->reader->addVariable('%ROOT%', ROOT);
        $this->reader->addVariable('%PROJECT_ROOT%', ROOT);

        putenv('DATABASE_URL=mysql://root:123456@127.0.0.1:3306/test');
        putenv('MONGO_URL=mongodb://localhost:27017');
        $yaml = '
root: %ROOT%
project_root: %ROOT%
database:
    url: %env(DATABASE_URL)%
mongo:
    url: %env(MONGO_URL)%';
        $data = $this->reader->fromString($yaml);
        $this->assertEquals(ROOT, $data['root']);
        $this->assertEquals(ROOT, $data['project_root']);
        $this->assertEquals(getenv('DATABASE_URL'), $data['database']['url']);
        $this->assertEquals(getenv('MONGO_URL'), $data['mongo']['url']);
    }
}