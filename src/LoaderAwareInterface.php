<?php

namespace Obullo\Config;

use Obullo\Config\LoaderInterface as Loader;

/**
 * Loader aware interface
 * 
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface LoaderAwareInterface
{
    /**
     * Set loader
     *
     * @param object $config config
     *
     * @return $this
     */
    public function setLoader(Loader $loader);

    /**
     * Get loader
     *
     * @return object
     */
    public function getLoader() : Loader;
}