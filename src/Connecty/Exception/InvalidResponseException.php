<?php

namespace Connecty\Exception;

/**
 * Invalid Response exception
 *
 * Thrown when a server responded with invalid or unexpected data (for example, a security hash did not match)
 */
class InvalidResponseException extends ConnectyException
{
    public function __construct($message = "Invalid response from server", $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
