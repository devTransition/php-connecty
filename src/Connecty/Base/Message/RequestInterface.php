<?php
/**
 * Request interface file
 */

namespace Connecty\Base\Message;

use Connecty\Base\Model\RequestDataInterface;
use Connecty\Exception\InvalidRequestException;
use GuzzleHttp\Client;

/**
 * Request Interface
 *
 * Interface class for definition for standard functions that any request should implement
 */
interface RequestInterface
{
    /**
     * @return Client
     */
    public function getHttpClient();

    /**
     * @return RequestAttributes
     */
    public function getRequestAttributes();

    /**
     * @return RequestDataInterface
     */
    public function getRequestData();

    /**
     * Get the response to this request (if the request has been sent)
     *
     * @return ResponseInterface
     */
    public function getResponse();

    /**
     * Initialize request with parameters
     *
     * @param RequestDataInterface $params The parameters to send
     */
    public function initializeRequestData(RequestDataInterface $params);

    /**
     * Send the request
     *
     * @return ResponseInterface
     */
    public function send();

    /**
     * Validate the request
     *
     * Override this method, it is called internally to avoid wasting time with an API call when the request is clearly invalid
     *
     * @throws InvalidRequestException If one mandatory parameter is missing or invalid
     * @return true If everything is ok.
     */
    public function validate();
}
