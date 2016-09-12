<?php

namespace Connecty\Base\Model;

use Connecty\Exception\RuntimeException;

/**
 * Interface DataModelInterface
 */
interface DataModelInterface
{
    /**
     * Convert the given array into object models
     *
     * @param array $data
     * @return static
     */
    public static function createFromArray(array $data);

    /**
     * Converts the current object into an array
     *
     * @throws RuntimeException If something could not converted
     * @return array
     */
    public function toArray();
}