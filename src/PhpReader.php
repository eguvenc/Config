<?php

namespace Obullo\Config;

use Zend\Config\Exception;
use Zend\Config\Reader\ReaderInterface;

/**
 * Default reader
 */
class PhpReader implements ReaderInterface
{
    /**
     * Directory of the JSON file
     *
     * @var string
     */
    protected $directory;

    /**
     * fromFile(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromFile()
     * @param  string $filename
     * @return array
     * @throws Exception\RuntimeException
     */
    public function fromFile($filename)
    {
        if (! is_file($filename) || ! is_readable($filename)) {
            throw new Exception\RuntimeException(sprintf(
                "File '%s' doesn't exist or not readable",
                $filename
            ));
        }
        $this->directory = dirname($filename);

        $config = require $filename;

        return $config;
    }

    /**
     * fromString(): defined by Reader interface.
     *
     * @param  string $string
     * @return array|bool
     * @throws Exception\RuntimeException
     */
    public function fromString($string)
    {
        return false;
    }
}