<?php
/**
 * Response interface file
 */

namespace Connecty\Base\Message;

/**
 * Response Interface
 *
 * Interface class that defines the standard functions
 */
interface ResponseInterface
{
    /**
     * Get the original request which generated this response
     *
     * @return RequestInterface
     */
    public function getRequest();
}
