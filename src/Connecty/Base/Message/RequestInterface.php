<?php
/**
 * Request interface file
 */

namespace Connecty\Base\Message;

/**
 * Request Interface
 *
 * Interface class for definition for standard functions that any request should implement
 */
interface RequestInterface
{
    /**
     * Initialize request with parameters
     *
     * @param array $params The parameters to send
     */
    public function initialize($params = []);

    /**
     * Get all request parameters
     *
     * @return array
     */
    public function getParameters();

    /**
     * Function to serialize the object to the HTTP request
     *
     * @return array
     */
    public function serialize();

    /**
     * Get the response to this request (if the request has been sent)
     *
     * @return ResponseInterface
     */
    public function getResponse();

    /**
     * Send the request
     *
     * @return ResponseInterface
     */
    public function send();

    /**
     * Send the request with specified data
     *
     * @param mixed $request_data The request data to send (body of request)
     * @return ResponseInterface
     */
    public function sendRequest($request_data);
}
