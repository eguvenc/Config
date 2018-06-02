<?php

namespace Obullo\Config;

use Obullo\Config\LoaderInterface;
use Obullo\Config\Reader\YamlReader;
use Obullo\Config\Cache\CacheInterface as CacheHandler;

use Zend\Config\Factory;
use Zend\Config\Reader\ReaderInterface;

/**
 * Config loader
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Loader implements LoaderInterface
{
    protected $env;

    /**
     * Set env
     * 
     * @param string $env environment variable
     */
    public function setEnv(string $env)
    {
        $this->env = $env;
    }

    /**
     * Returns to environment variable
     * 
     * @return mixed
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * Register reader
     * 
     * @return void
     */
    public function registerReader(string $loader = 'yaml', ReaderInterface $reader)
    {
        Factory::registerReader($loader, $reader);
    }

    /**
     * Load configuration files
     * 
     * @param  string  $filename filename
     * @param  boolean $object   returns to zend config object
     * 
     * @return array|object
     */
    public function load(string $root, string $filename, $object = false, $useIncludePath = false)
    {
        $file = str_replace('%s', $this->getEnv(), $filename);

        return Factory::fromFile($root.'/'.ltrim($file, '/'), $object, $useIncludePath = false);
    }

    /**
     * Read configuration from multiple files and merge them.
     *
     * @param  array   $files
     * @param  bool $returnConfigObject
     * @param  bool $useIncludePath
     * @return array|Config
     */
    public function loadFiles(array $files, $returnConfigObject = false, $useIncludePath = false)
    {
        $filenames = [];
        foreach ($files as $key => $value) {
            $filenames[$key] = str_replace('%s', $this->getEnv(), $value);
        }
        return Factory::fromFiles($filenames, $returnConfigObject, $useIncludePath);
    }
}