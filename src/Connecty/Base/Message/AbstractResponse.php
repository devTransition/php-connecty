<?php
/**
 * Abstract Response class file
 */

namespace Connecty\Base\Message;

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
     * Request object for Response
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * The plain data contained in the response
     *
     * @var mixed
     */
    protected $response_data;

    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request
     * @param mixed $response_data
     */
    public function __construct(RequestInterface $request, $response_data)
    {
        $this->request = $request;
        $this->response_data = $response_data;
    }

    /**
     * Get the initiating request object
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get return code for response
     *
     * @return int
     */
    public function getCode()
    {
        return null;
    }

    /**
     * Get the response data
     *
     * @return mixed
     */
    public function getResponseData()
    {
        return $this->response_data;
    }
}
