<?php

namespace Obullo\Config\Cache;

/**
 * Cache interface
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface CacheInterface
{
    /**
     * Checks the file has cached
     * 
     * @param  string $file filename
     * @return boolean|array
     */
    public function has(string $file);

    /**
     * Read file
     * 
     * @param  string $file file
     * @return string
     */
    public function read(string $file) : array;

    /**
     * Write to cache
     * 
     * @param  string $file  file
     * @param  data   $data  array
     * @return void
     */
    public function write(string $file, array $data);
}