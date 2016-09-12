<?php
/**
 * Abstract Response class file
 */

namespace Connecty\Base\Message;

use Connecty\Base\Model\DataModelInterface;
use Connecty\Exception\InvalidResponseException;

/**
 * Abstract Response
 *
 * This class defines basic functions that all Responses should implement
 *
 * Example:
 *
 * <code>
 *   $response = $request->send();
 *   // then process the $response object
 * </code>
 *
 * @see ResponseInterface
 */
abstract class AbstractResponse implements ResponseInterface
{
    /**
     * Holds the response data
     *
     * @var DataModelInterface
     */
    protected $data;

    /**
     * Holds the http status code
     *
     * @see https://de.wikipedia.org/wiki/HTTP-Statuscode
     *
     * @return int
     */
    protected $status_code;

    /**
     * Holds the error messages that occurred by the request
     *
     * @var DataModelInterface
     */
    protected $errors;

    /**
     * Constructor
     *
     * @param int $status_code
     * @param DataModelInterface $data
     * @param DataModelInterface $errors
     */
    public function __construct($status_code, DataModelInterface $data = null, DataModelInterface $errors = null)
    {
        $this->status_code = (int)$status_code;
        $this->data = $data;
        $this->errors = $errors;
    }

    /**
     * Get the response data
     *
     * @return DataModelInterface
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the http status code
     *
     * @see https://de.wikipedia.org/wiki/HTTP-Statuscode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * Get the error messages that occurred by the request
     *
     * @return DataModelInterface
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Validate the response data (check status code, check for existing error messages in the response, ...)
     *
     * @throws InvalidResponseException If something is wrong with the response
     * @return static
     */
    abstract public function validate();
}
