<?php

namespace Obullo\Config;

use Obullo\Config\PhpReader;

use Zend\Config\Config;
use Zend\Config\Factory;
use Zend\Config\Reader\ReaderInterface;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\Config\Processor\ProcessorInterface;

/**
 * File loader
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ConfigLoader implements ConfigLoaderInterface
{
    protected $env;
    protected $config;
    protected $processors = array();
    protected $cachedConfigFile;

    /**
     * Constructor
     * 
     * @param array  $config           config
     * @param string $cachedConfigFile cache file
     */
    public function __construct(array $config, string $cachedConfigFile)
    {
        $this->config = $config;
        $this->cachedConfigFile = $cachedConfigFile;
    }

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
     * Add processor
     * 
     * @param ProcessorInterface $processor object
     */
    public function addProcessor(ProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    /**
     * Load single file
     * 
     * @param  string  $root               root path
     * @param  string  $filename           file url
     * 
     * @return mixed
     */
    public function load(string $root, string $filename)
    {
        $filename  = str_replace('%s', $this->getEnv(), $filename);
        $file = rtrim($root, '/').'/'.ltrim($filename, '/');

        $parts = pathinfo($file);
        $name  = $parts['filename'];
        $localConfig = $this->loadConfigFromCache($name, $this->cachedConfigFile);

        $reader = Self::getReader($parts['extension']);

        if (false == $localConfig) {
            $localConfig = $reader->fromFile($file);
            $this->cacheConfig($name, $localConfig, $this->cachedConfigFile);
        } else {
            $localConfig = $this->config['LOCAL'][$name];
        }
        $config = new Config($localConfig, true);

        foreach ($this->processors as $processor) {
            $processor->process($config);
        }
        return $config;
    }
    
    /**
     * Get reader object
     * 
     * @param  string $reader name
     * @return object
     */
    protected static function getReader(string $reader) : ReaderInterface
    {
        if ($reader == null | $reader == 'php') {
            return new PhpReader;
        }
        $plugin = Factory::getReaderPluginManager();
        return $plugin->get($reader);
    }

    /**
     * Attempt to load the configuration from a cache file.
     *
     * @param null|string $cachedConfigFile
     * @return bool
     */
    private function loadConfigFromCache(string $name, $cachedConfigFile)
    {
        if (null === $cachedConfigFile || false == $this->config['config_cache_enabled']) {
            return false;
        }
        if (! file_exists($cachedConfigFile)) {
            return false;
        }
        if (! isset($this->config['LOCAL'][$name])) {  // Allows to write local config files to cache
            return false;
        }
        return $this->config['LOCAL'][$name];
    }

    /**
     * Attempt to cache discovered configuration.
     *
     * @param array $config
     * @param null|string $cachedConfigFile
     */
    private function cacheConfig(string $name, array $localConfig, $cachedConfigFile)
    {
        if (null === $cachedConfigFile) {
            return;
        }
        if (false == $this->config['config_cache_enabled']) {
            return;
        }
        if (file_exists($cachedConfigFile)) {
            unlink($cachedConfigFile);
        }
        $this->config['LOCAL'][$name] = $localConfig;

        file_put_contents($cachedConfigFile, sprintf(
            ConfigAggregator::CACHE_TEMPLATE,
            get_class($this),
            date('c'),
            var_export($this->config, true)
        ));
    }
}