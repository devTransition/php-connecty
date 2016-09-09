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
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_HEAD = 'HEAD';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_TRACE = 'TRACE';

    /**
     * @var string
     */
    private $method;

    /**
     * Path part of url
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $options;

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
     * @return static
     */
    public function addOptions($options = [])
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the options array for the http client
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
