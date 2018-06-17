<?php

namespace Obullo\Config;

/**
 * Config loader interface
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ConfigLoaderInterface
{    
    /**
     * Load single file
     *
     * @param  string  $root               root path
     * @param  string  $filename           file url
     * 
     * @return mixed
     */
    public function load(string $root, string $filename);
}