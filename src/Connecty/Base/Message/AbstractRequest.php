<?php
/**
 * Abstract Request class file
 */

namespace Connecty\Base\Message;

use Connecty\Base\Model\RequestDataInterface;
use Connecty\Exception\InvalidRequestException;
use Connecty\Exception\InvalidResponseException;
use Connecty\Exception\RuntimeException;
use GuzzleHttp\Client;

/**
 * Abstract Request
 *
 * This class defines a set of functions for all Connecty requests
 *
 * Requests are usually created by calling createRequestName function on Connecty subclass
 *
 * Requests are model classes to model request bodies for different API endpoints
 * Public properties of Request are considered as request_data that should be inside request body when request is sent
 *
 * Example -- creating a request:
 *
 * <code>
 *   class MyRequest extends \Connecty\Message\AbstractRequest {};
 *
 *   class MyClient extends \Connecty\Connecty {
 *     function myRequest($params) {
 *       return new MyRequest($params);
 *     }
 *   }
 *
 *   $config = [];
 *   // Create the connecty subclass
 *   $client = new MyClient($config);
 *
 *   // Create the request object
 *   $my_request = $client->myRequest($request_params);
 * </code>
 *
 * Example -- validating and sending a request:
 *
 * <code>
 *   try {
 *     $response = $my_request->send();
 *   } catch (InvalidRequestException $e) {
 *     print "Something went wrong: " . $e->getMessage() . "\n";
 *   }
 *   // now work with $response
 * </code>
 *
 * @see RequestInterface
 * @see AbstractResponse
 */
abstract class AbstractRequest implements RequestInterface
{
    /**
     * The request client
     *
     * @var Client
     */
    protected $http_client;

    /**
     * @var RequestDataInterface
     */
    protected $request_data;

    /**
     * The request attributes
     *
     * @var RequestAttributes
     */
    protected $request_attributes;

    /**
     * An associated ResponseInterface.
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Create a new Request
     *
     * @param Client $http_client A Guzzle client to make API calls with
     * @param RequestDataInterface $request_data
     * @param RequestAttributes $request_attributes
     */
    public function __construct(Client $http_client, RequestDataInterface $request_data = null, RequestAttributes $request_attributes = null)
    {
        // set up the http client
        $this->http_client = $http_client;

        // set up the (early binding of the) request data
        if ($request_data) {
            $this->initializeRequestData($request_data);
        }

        // set up the (optional) given and the default request attributes
        $this->initializeRequestAttributes($request_attributes);
    }

    /**
     * Avoid cloning an instance of this object
     */
    private function __clone()
    {
    }

    /**
     * Function that initializes request_attributes for current class
     * @param RequestAttributes $request_attributes
     * @return static
     */
    protected function initializeRequestAttributes(RequestAttributes $request_attributes = null)
    {
        $this->request_attributes = $request_attributes ? $request_attributes : new RequestAttributes();
        return $this;
    }

    /**
     * Initialize the object with parameters.
     *
     * If any unknown parameters passed, they will be ignored.
     *
     * @param RequestDataInterface $request_data An associative array of parameters
     * @throws RuntimeException
     * @return static
     */
    public function initializeRequestData(RequestDataInterface $request_data)
    {
        if ($this->response) {
            throw new RuntimeException('Request cannot be modified after it has been sent');
        }

        $this->request_data = $request_data;

        return $this;
    }

    /**
     * Validate the request
     *
     * Override this method, it is called internally to avoid wasting time with an API call when the request is clearly invalid
     *
     * @throws InvalidRequestException If one mandatory parameter is missing or invalid
     * @return true If everything is ok.
     */
    abstract public function validate();

    /**
     * Send the request
     *
     * @return ResponseInterface
     * @throws RuntimeException
     */
    public function send()
    {
        if (!$this->validate()) {
            throw new RuntimeException('Request ' . get_class($this) . ' is not valid');
        }

        return $this->sendRequest($this->request_data);
    }

    /**
     * Send the request with specified data
     *
     * @param RequestDataInterface $request_data The request data to send (body of request)
     * @throws RuntimeException On any RequestException of the http client
     * @throws InvalidRequestException If one mandatory parameter is missing or invalid
     * @throws InvalidResponseException If something is wrong with the response
     * @return ResponseInterface
     */
    abstract protected function sendRequest(RequestDataInterface $request_data);

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->http_client;
    }

    /**
     * @return RequestDataInterface
     */
    public function getRequestData()
    {
        return $this->request_data;
    }

    /**
     * @return RequestAttributes
     */
    public function getRequestAttributes()
    {
        return $this->request_attributes;
    }

    /**
     * @throws RuntimeException
     * @return ResponseInterface
     */
    public function getResponse()
    {
        if ($this->response === null) {
            throw new RuntimeException('You must call send() before accessing the Response');
        }

        return $this->response;
    }
}
