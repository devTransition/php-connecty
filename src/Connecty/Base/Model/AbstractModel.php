<?php

namespace Connecty\Base\Model;

use Connecty\Exception\RuntimeException;

/**
 * Class AbstractModel
 * @package ConnectyApiv2\Base\Model
 */
abstract class AbstractModel implements DataModelInterface
{
    /**
     * Convert the given array into object models
     *
     * @param array $data
     * @return static
     */
    public static function createFromArray(array $data)
    {
        $o = new static();

        foreach ($o->getParameters() as $name) {
            if (isset($data[$name])) {
                $new_value = $data[$name];

                $converted_name = str_replace('_', '', ucwords($name, '_'));

                // Do we need to convert a multidimensional array?
                if (is_array($data[$name])) {
                    $class_name = 'ConnectyApiv2\\Base\\Model\\' . $converted_name;

                    // Check if there is an model class for the given parameter name
                    if (class_exists($class_name)) {

                        // Check if the the current value data is an associative array
                        if (count($data[$name]) == count(array_filter(array_keys($data[$name]), function ($key){ return is_numeric($key); }))) {
                            // Associative array: convert to an associative array with one ore more objects
                            $new_value = [];
                            foreach ($data[$name] as $item) {
                                $new_value[] = $class_name::createFromArray($item);
                            }
                        } else {
                            // No associative array: convert directly to an object
                            $new_value = $class_name::createFromArray($data[$name]);
                        }

                    }
                }

                // Add the converted value to the object
                $o->{'set'.$converted_name}($new_value);
            }
        }

        return $o;
    }

    /**
     * Default getParameters function
     *
     * @return array
     */
    private function getParameters()
    {
        $ret = [];
        $reflect = new \ReflectionObject($this);
        foreach ($reflect->getProperties(\ReflectionProperty::IS_PROTECTED) as $property) {
            $ret[] = $property->getName();
        }

        return $ret;
    }

    /**
     * Converts the current object into an array
     *
     * @throws RuntimeException If something could not converted
     * @return array
     */
    public function toArray()
    {
        $ret = [];
        $reflect = new \ReflectionObject($this);
        foreach ($reflect->getProperties(\ReflectionProperty::IS_PROTECTED) as $property) {
            $property_name = $property->getName();
            $value = $this->{'get'.str_replace('_', '', ucwords($property_name, '_'))}();

            if (null === $value) {
                continue;
            }

            if (is_object($value)) {

                if (!is_callable([$value, 'toArray'])) {
                    throw new RuntimeException('Convert an object to array need the function "toArray" to work! class: ' . get_class($value));
                }
                $ret[$property_name] = $value->toArray();

            } elseif (is_array($value)) {

                foreach ($value as $key => $item) {
                    if (null === $item) {
                        continue;
                    }
                    $ret[$property_name][$key] = is_object($item) ? $item->toArray() : $item;
                }

            } else {
                $ret[$property_name] = $value;
            }
        }

        return $ret;
    }
}