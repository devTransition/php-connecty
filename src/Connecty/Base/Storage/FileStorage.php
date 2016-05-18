<?php
/**
 * FileStorage class file
 */

namespace Connecty\Base\Storage;

use Connecty\Exception\ConnectyException;
use Psr\Http\Message\StreamInterface;

/**
 * Class to use temporary file as storage
 */
class FileStorage extends MemoryStorage
{
    /**
     * Storage data
     * @var string
     */
    private $dir;

    /**
     * FileStorage constructor
     *
     * @param string $dir
     * @throws ConnectyException
     */
    public function __construct($dir)
    {
        parent::__construct();
        if (is_file($dir)) {
            throw new ConnectyException('Invalid storage location, file with same name already exists');
        }
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $this->dir = $dir;
        $this->load();
    }

    /**
     * Retrieve an item from cache
     *
     * @param string $key - The key to store it under
     * @return mixed
     */
    public function get($key)
    {
        if (isset($this->storage[$key])) {
            return $this->storage[$key];
        }
        $file = $this->findFile($key);
        if ($file === false) {
            return false;
        }
        if ($file === 0) {
            return null;
        }
        return fopen($file, 'r');
    }

    /**
     * Set an item in the cache
     *
     * @param string $key - The key to store it under
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value)
    {
        // take care about replacing keys !
        if (is_resource($value) || $value instanceof StreamInterface) {
            $res = file_put_contents($this->filePath($key), $value);
            if ($res !== false) {
                // clear plain storage from key
                return $this->deleteStore($key);
            }
            return $res;
        } else {
            $this->storage[$key] = $value;
            $res = $this->save();
            if ($res !== false) {
                // clear file storage from key
                return $this->deleteFile($key);
            }
            return $res;
        }
    }

    /**
     * Remove a key from the cache.
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        $res = $this->deleteFile($key);
        if ($res === false) {
            return false;
        }
        return $this->deleteStore($key);
    }

    /**
     * Remove all items from the cache (flush it)
     *
     * @return bool
     */
    public function deleteAll()
    {
        try {
            array_map('unlink', glob($this->filePath('*')));
        } catch (\Exception $e) {
            return false;
        }
        $this->storage = [];
        return true;
    }

    /**
     * Function that loads storage
     *
     * @return bool
     */
    private function load()
    {
        $filename = $this->filePath();
        $data = @file_get_contents($filename);
        if ($data) {
            $this->storage = json_decode($data, true);
            return true;
        }
        return false;
    }

    /**
     * Function to save current storage
     *
     * @return bool
     */
    private function save()
    {
        file_put_contents($this->filePath(), json_encode($this->storage));
        return true;
    }

    private function findFile($key)
    {
        $files = glob($this->filePath($key));
        if ($files === false) {
            return false;
        }
        $c = count($files);
        if ($c === 0) {
            return 0;
        }
        if ($c === 1) {
            return $files[0];
        }
        // too much files found, error, should not happen...
        return false;
    }

    private function deleteFile($key)
    {
        $file = $this->findFile($key);
        if ($file === false) {
            return false; // error
        }
        if ($file === 0) {
            return true; // nothing found
        }
        return unlink($file);
    }

    private function deleteStore($key)
    {
        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);
            return $this->save();
        }
        return true;
    }

    /**
     * Function to return default file to store the storage data
     *
     * @param string $file default null
     * @return string
     */
    private function filePath($file = null)
    {
        // use  default name for json data
        return $this->dir . DIRECTORY_SEPARATOR . ($file == null ? 'YmFuYW5lDQo' : $file);
    }
}