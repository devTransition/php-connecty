<?php
/**
 * Response interface file
 */

namespace Connecty\Base\Message;
use Connecty\Base\Model\DataModelInterface;

/**
 * Response Interface
 *
 * Interface class that defines the standard functions
 */
interface ResponseInterface
{
    /**
     * Get the response data
     *
     * @return DataModelInterface
     */
    public function getData();

    /**
     * Get the http status code
     *
     * @see https://de.wikipedia.org/wiki/HTTP-Statuscode
     *
     * @return int
     */
    public function getStatusCode();

    /**
     * Get the error messages that occurred by the request
     *
     * @return DataModelInterface
     */
    public function getErrors();
}
