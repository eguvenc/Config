<?php

namespace Obullo\Config\Cache;

/**
 * File handler
 *
 * @copyright Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class FileHandler implements CacheInterface
{
    protected $path;

    /**
     * Set save path
     * 
     * @param string $path path
     */
    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/');
    }

    /**
     * Checks the file has cached
     * 
     * @param  string $file filename
     * @return boolean|array
     */
    public function has(string $file)
    {
        $key = Self::getKey($file, $this->path);
        $mtime = filemtime($file);
        if (file_exists($key)) {
            $serializedData = file_get_contents(Self::getKey($file, $this->path));
            $data = unserialize($serializedData);
            $time = (int)$data['__mtime__'];
            if ($mtime > $time) {
                $this->delete($file);
                return false;
            }
            unset($data['__mtime__']);
            return $data;
        }
        return false;
    }

    /**
     * Read file
     * 
     * @param  string $file file
     * @return string
     */
    public function read(string $file) : array
    {
        $mtime = filemtime($file);
        $serializedData = file_get_contents(Self::getKey($file, $this->path));
        $data = unserialize($serializedData);
        unset($data['__mtime__']);
        return $data;
    }

    /**
     * Write to cache
     * 
     * @param  string $file  file
     * @param  data   $data  array
     * @return void
     */
    public function write(string $file, array $data)
    {
        if (! is_dir(ROOT.$this->path)) {
            mkdir(ROOT.$this->path, 0777);
        }
        $key = Self::getKey($file, $this->path);
        $data['__mtime__'] = filemtime($file);
        $serializedData = serialize($data);
        file_put_contents($key, $serializedData);
    }

    /**
     * Delete file
     * 
     * @param  string $file file
     * @return void
     */
    public function delete(string $file)
    {
        $key = Self::getKey($file, $this->path);
        unlink($key);
    }

    /**
     * Returns to normalized key
     * 
     * @param  string $file file
     * @return string
     */
    protected static function getKey(string $file, string $path)
    {
        $filestr  = str_replace(array(ROOT,'/'), array('',':'), $file);
        $filename = strstr($filestr, '.', true);

        return ROOT.$path.'/'.ltrim($filename, ':');
    }
}