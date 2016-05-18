<?php
/**
 * Storage interface
 */
namespace Connecty\Base\Storage;

interface StorageInterface
{
    /**
     * Retrieve an item from cache
     *
     * @param string $key The key to store it under
     * @return mixed
     */
    function get($key);

    /**
     * Set an item in the cache
     *
     * @param string $key
     * @param mixed $value
     */
    function set($key, $value);

    /**
     * Remove a key from the cache
     *
     * @param string $key
     */
    function delete($key);

    /**
     * Remove all items from the cache (flush it)
     */
    function deleteAll();
}