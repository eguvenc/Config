<?php

use Obullo\Config\PhpReader;
use PHPUnit\Framework\TestCase;

class PhpReaderTest extends TestCase
{
    public function testFromFile()
    {
        $reader = new PhpReader;
        $data = $reader->fromFile(ROOT.'/tests/var/config/database.php');

        $this->assertEquals($data['url'], 'env(DATABASE_URL)');
    }
}
