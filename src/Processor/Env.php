<?php

namespace Obullo\Config\Processor;

use Zend\Config\Config;
use Zend\Config\Exception;
use Zend\Config\Processor\Token;
use Zend\Config\Processor\ProcessorInterface;

/**
 * Env processor for Zend/Config
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Env extends Token implements ProcessorInterface
{
    /**
     * Override processing of individual value.
     *
     * If the value is a string and evaluates to a getenv function, returns
     * the getenv() method value; otherwise, delegates to the parent.
     *
     * @param mixed $value
     * @param array $replacements
     * @return mixed
     */
    protected function doProcess($value, array $replacements)
    {
        if (! is_string($value)) {
            return parent::doProcess($value, $replacements);
        }

        if (false === strpos($value, 'env(')) {
            return parent::doProcess($value, $replacements);
        }
        $value = $this->parseEnvRecursive($value);

        return $value;
    }

    /**
     * Parse env variables
     * 
     * @param mixed $input input
     * @return string
     */
    protected function parseEnvRecursive($input)
    {
        $regex = '/env\((.*?)\)/';
        if (is_array($input)) {
            $input = getenv($input[1]);
        }
        return preg_replace_callback($regex, array($this, 'parseEnvRecursive'), $input);
    }
}