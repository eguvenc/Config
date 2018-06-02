<?php

namespace Obullo\Config;

use Zend\Config\Reader\ReaderInterface;

/**
 * Loader interface
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface LoaderInterface
{    
    /**
     * Set env
     * 
     * @param string $env environment variable
     */
    public function setEnv(string $env);

    /**
     * Returns to environment variable
     * 
     * @return string
     */
    public function getEnv();

    /**
     * Register reader
     * 
     * @return void
     */
    public function registerReader(string $loader = 'yaml', ReaderInterface $reader);

    /**
     * Read configuration from multiple files and merge them.
     *
     * @param  array   $files
     * @param  bool $returnConfigObject
     * @param  bool $useIncludePath
     * @return array|Config
     */
    public function loadFiles(array $files, $returnConfigObject = false, $useIncludePath = false);

    /**
     * Load configuration files
     * 
     * @param  string  $filename filename
     * @param  boolean $object   returns to zend config object
     * 
     * @return array|object
     */
    public function load(string $root, string $filename, $object = false, $useIncludePath = false);
}