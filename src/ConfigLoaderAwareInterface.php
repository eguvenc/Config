<?php

namespace Obullo\Config;

use Obullo\Config\ConfigLoaderInterface as Loader;

interface ConfigLoaderAwareInterface
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