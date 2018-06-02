<?php

namespace Obullo\Config\Reader;

use Zend\Config\Exception;
use Symfony\Component\Yaml\Yaml;
use Zend\Config\Reader\ReaderInterface;
use Obullo\Config\AbstractReader;

/**
 * YAML config reader for Zend\Config
 */
class YamlReader extends AbstractReader implements ReaderInterface
{
    /**
     * Directory of the YAML file
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
        
        $config = $this->cache->has($filename);
        if (false == $config) {
            $config = Yaml::parse(file_get_contents($filename));
            $this->cache->write($filename, $config);   
        }
        if (null === $config) {
            throw new Exception\RuntimeException("Error parsing YAML data");
        }

        return $this->process($config);
    }

    /**
     * fromString(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromString()
     * @param  string $string
     * @return array|bool
     * @throws Exception\RuntimeException
     */
    public function fromString($string)
    {
        if (empty($string)) {
            return [];
        }
        $this->directory = null;

        $config = Yaml::parse($string);

        if (null === $config) {
            throw new Exception\RuntimeException("Error parsing YAML data");
        }

        return $this->process($config);
    }

    /**
     * Process the array for @include
     *
     * @param  array $data
     * @return array
     * @throws Exception\RuntimeException
     */
    protected function process(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->process($value);
            } else {
                $data[$key] = $this->parseEnvRecursive($value);
            }
            if (trim($key) === '@include') {
                if ($this->directory === null) {
                    throw new Exception\RuntimeException('Cannot process @include statement for a json string');
                }
                $reader = clone $this;
                unset($data[$key]);
                $data = array_replace_recursive($data, $reader->fromFile($this->directory . '/' . $value));
            }
        }
        return $data;
    }
}