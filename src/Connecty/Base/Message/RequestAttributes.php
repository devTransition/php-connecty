<?php
/**
 * Request Attributes class file
 */

namespace Connecty\Base\Message;

/**
 * Request attributes
 *
 * class that covers guzzle request parameters as one class
 */
class RequestAttributes
{
    /**
     * Constant definitions
     */
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * @var string
     */
    public $method;

    /**
     * Path part of url
     * @var string
     */
    public $path;

    /**
     * @var array
     */
    public $options;

    /**
     * Create a RequestParameters
     *
     * @param string $method
     * @param string $path
     * @param array $options
     */
    public function __construct($method = self::METHOD_POST, $path = '', $options = [])
    {
        $this->method = $method;
        $this->path = $path;
        $this->options = $options;
    }

    /**
     * Function to add options to Request parameters
     *
     * @param array $options
     */
    public function addOptions($options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

}
