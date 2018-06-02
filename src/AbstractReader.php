<?php

namespace Obullo\Config;

use Obullo\Config\Cache\CacheInterface as CacheHandler;

/**
 * Common config reader
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
abstract class AbstractReader
{
    /**
     * Config cache
     * 
     * @var object
     */
    protected $cache;

    /**
     * Config variables
     * 
     * @var string
     */
    protected $variables;

    /**
     * Constructor
     * 
     * @param CacheHandler $cache cache
     * @param string $root project root path
     */
    public function __construct(CacheHandler $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Add config variables
     * 
     * @param string $key   variable string
     * @param mxied  $value variable value
     */
    public function addVariable(string $key, $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * Parse env variables
     * 
     * @param mixed $input input
     * @return string
     */
    protected function parseEnvRecursive($input)
    {
        if (is_string($input) && ! empty($this->variables)) {
            $input = str_replace(array_keys($this->variables),array_values($this->variables),$input);
        }
        $regex = '/%env\((.*?)\)%/';
        if (is_array($input)) {
            $input = getenv($input[1]);
        }
        return preg_replace_callback($regex, array($this, 'parseEnvRecursive'), $input);
    }
}