<?php
/**
 * Abstract Request class file
 */

namespace Connecty\Base\Message;

use GuzzleHttp\Client;
use Connecty\Exception\RuntimeException;
use Connecty\Base\Helper;

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
     * The request attributes
     *
     * @var RequestAttributes
     */
    protected $request_attributes;

    /**
     * The request client
     *
     * @var Client
     */
    protected $http_client;

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
     * @param array $request_data
     * @param RequestAttributes $request_attributes
     */
    public function __construct(Client $http_client, $request_data = [], RequestAttributes $request_attributes = null)
    {
        $this->http_client = $http_client;

        $this->initialize($request_data);

        if ($request_attributes) {
            $this->request_attributes = $request_attributes;
        } else {
            $this->_initializeRequestAttributes();
        }
    }

    /**
     * Function that initializes request_attributes for current class
     */
    protected abstract function _initializeRequestAttributes();

    /**
     * Initialize the object with parameters.
     *
     * If any unknown parameters passed, they will be ignored.
     *
     * @param array $request_data An associative array of parameters
     *
     * @return $this
     * @throws RuntimeException
     */
    public function initialize($request_data = [])
    {
        if (null !== $this->response) {
            throw new RuntimeException('Request cannot be modified after it has been sent');
        }

        Helper::initialize($this, $request_data);

        return $this;
    }

    /**
     * Default getParameters function
     *
     * @return array
     */
    public function getParameters()
    {
        $ret = [];
        $reflect = new \ReflectionObject($this);
        foreach ($reflect->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $ret[] = $property->getName();
        }

        return $ret;
    }

    /**
     * Validate the request
     *
     * Override this method, it is called internally to avoid wasting time with an API call when the request is clearly invalid
     *
     * @return bool
     */
    public function validate()
    {
        return false;
    }

    /**
     * Serialize the request_data to a return array
     *
     * you can override this method and use some marshaller objects to serialize your request
     *
     * @return array $ret
     */
    public function serialize()
    {
        $ret = [];
        $reflect = new \ReflectionObject($this);
        foreach ($reflect->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            $property_name = $prop->getName();
            $ret[$property_name] = $this->$property_name;
        }

        return $ret;
    }

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
        $request_data = $this->serialize();

        return $this->sendRequest($request_data);
    }

    /**
     * Get the associated Response
     *
     * @return ResponseInterface
     * @throws RuntimeException
     */
    public function getResponse()
    {
        if ($this->response === null) {
            throw new RuntimeException('You must call send() before accessing the Response');
        }

        return $this->response;
    }
}
