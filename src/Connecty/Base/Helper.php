<?php
/**
 * Helper class file
 */

namespace Connecty\Base;

/**
 * Helper class defines various static utility functions that are in use
 */
class Helper
{
    /**
     * Convert a string to camelCase. Strings already in camelCase will not be harmed
     *
     * @param string $str The input string
     * @return string $str camelCased output string
     */
    public static function camelCase($str)
    {
        $str = self::convertToLowercase($str);
        return preg_replace_callback(
            '/_([a-z])/',
            function ($match) {
                return strtoupper($match[1]);
            },
            $str
        );
    }

    /**
     * Convert strings with underscores to be all lowercase
     *
     * @param string $str The input string
     * @return string The output string
     */
    protected static function convertToLowercase($str)
    {
        $exploded_str = explode('_', $str);

        if (count($exploded_str) > 1) {
            $lowercase_str = [];
            foreach ($exploded_str as $value) {
                $lowercase_str[] = strtolower($value);
            }
            $str = implode('_', $lowercase_str);
        }

        return $str;
    }

    /**
     * Validate a card number according to the Luhn algorithm
     *
     * @param string $card_number to validate
     * @return bool true if the card_number is valid
     */
    public static function validateLuhn($card_number)
    {
        $str = '';
        foreach (array_reverse(str_split($card_number)) as $i => $c) {
            $str .= $i % 2 ? $c * 2 : $c;
        }

        return array_sum(str_split($str)) % 10 === 0;
    }

    /**
     * Function to initialize an object with a given array of parameters
     *
     * @param mixed $target The object to set parameters on
     * @param array $params An array of parameters to set
     */
    public static function initialize($target, $params)
    {
        if (is_array($params)) {
            $reflection_obj = new \ReflectionObject($target);
            foreach ($params as $key => $value) {
                if ($reflection_obj->hasProperty($key)) {
                    $target->$key = $value;
                }
            }
        }
    }

    /**
     * Convert an amount into a float
     *
     * @var string|int|float $value
     * @return float The amount converted to a float
     * @throws \InvalidArgumentException
     */
    public static function toFloat($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            throw new \InvalidArgumentException('Data type is not a valid decimal number');
        }

        if (is_string($value)) {
            // Validate generic number, with optional sign and decimals.
            if (!preg_match('/^[-]?[0-9]+(\.[0-9]*)?$/', $value)) {
                throw new \InvalidArgumentException('String is not a valid decimal number');
            }
        }

        return (float)$value;
    }

    /**
     * Decodes an JSON string into a object with properties of standard PHP types, including stdClass or assoc array
     *
     * @param string $json_str The string to decode
     * @param boolean $assoc If true a assoc array is returned
     * @return mixed The created object, never null or other
     * @throws \InvalidArgumentException
     */
    public static function jsonDecode($json_str, $assoc = false)
    {
        $data = \json_decode($json_str, $assoc);
        if (json_last_error() !== JSON_ERROR_NONE) {
            switch (json_last_error()) {
                case JSON_ERROR_DEPTH:
                    $message = ' - Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $message = ' - Underflow or the modes mismatch';
                    break;
                case JSON_ERROR_UTF8:
                    $message = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $message = ' - Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $message = ' - Syntax error, malformed JSON';
                    break;
                default:
                    $message = 'Unknown error';
            }
            throw new \InvalidArgumentException('Unable to parse JSON data: ' . $message);
        }
        return $data;
    }
}
