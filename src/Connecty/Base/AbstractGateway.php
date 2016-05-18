<?php
/**
 * Base Gateway class file
 */

namespace Connecty\Base;

use Connecty\Base\Message\AbstractRequest;
use Connecty\Base\Message\RequestAttributes;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Connecty\Base\Storage\StorageInterface;
use Connecty\Base\Storage\MemoryStorage;
use Psr\Log\LoggerInterface;
use Connecty\Base\Log\Logger;
use Connecty\Base\Log\GuzzleLogger;

/**
 * Base gateway class
 *
 * This class provides the wrapper for every gateway
 *
 * Example:
 *
 * <code>
 *   // Initialise the gateway
 *   $gateway->initialize(...);
 *
 *   // do the request on the gateway
 *   // Do an authorisation transaction on the gateway
 *   $gateway->authorize(...);
 * </code>
 */
abstract class AbstractGateway
{
    /**
     * Constants definition
     */
    const PRODUCTION_MODE = 0;
    const TEST_MODE = 1;
    const SIMULATE_MODE = 2;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $http_client;

    /**
     * Configuration
     * @var array
     */
    protected $config;

    /**
     * Logger used for logging
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;

    /**
     * Storage used to store authorization and caching
     * @var StorageInterface
     */
    public $storage;

    /**
     * Test mode integer
     * @var int
     */
    private $test_mode;

    /**
     * Create a new gateway instance
     *
     * @param array $config
     * @param \GuzzleHttp\Client $http_client - A Guzzle client to make API calls with
     * @param LoggerInterface $logger - the logger that will be used for gateway
     * @param StorageInterface $storage - special cache for storing variables for gateway
     */
    public function __construct($config = [], Client $http_client = null, LoggerInterface $logger = null, StorageInterface $storage = null)
    {
        $this->config = $config;

        $this->http_client = $http_client ?: $this->getDefaultHttpClient();

        // initialize default logger with logging disabled if not provided
        $this->logger = $logger !== null ? $logger : new Logger(null, false);

        $this->storage = $storage !== null ? $storage : new MemoryStorage();

        $this->test_mode = self::PRODUCTION_MODE;
        if (!empty($config['test_mode'])) {
            $this->setTestMode($config['test_mode']);
        }

        Helper::initialize($this, $config);
    }

    /**
     * Get the alias of the Gateway
     *
     * @return string $alias
     */
    public function getAlias()
    {
        return Helper::getGatewayAlias(get_class($this));
    }

    /**
     * Test mode getter
     * @return int
     */
    public function getTestMode()
    {
        return $this->test_mode;
    }

    /**
     * Test mode setter
     * @param int $value
     */
    public function setTestMode($value)
    {
        $this->test_mode = $value;
    }

    /**
     * Initialize this gateway with array parameters
     *
     * (this method expects that gateway class has defined setters for parameters)
     *
     * @param array $params
     * @return $this
     */
    public function initialize($params = [])
    {
        Helper::initialize($this, $params);
        return $this;
    }

    /**
     * Create and initialize a request object
     *
     * <code>
     *   class MyRequest extends \Connecty\Base\Message\AbstractRequest {};
     *
     *   class MyGateway extends \Connecty\Base\AbstractGateway {
     *     function myRequest($request_data) {
     *       $this->createRequest('MyRequest', $request_data);
     *     }
     *   }
     *
     *   // Create the gateway object
     *   $gw = Connecty::create('MyGateway');
     *
     *   // Create the request object
     *   $my_request = $gw->myRequest($request_params);
     * </code>
     *
     * @see AbstractRequest
     * @param string $class - request class name
     * @param array $params
     * @param RequestAttributes $request_attributes
     * @return AbstractRequest
     */
    protected function createRequest($class, $params, $request_attributes = null)
    {
        /** @var AbstractRequest $obj */
        $obj = new $class($this->http_client, $params, $request_attributes);

        return $obj;
    }

    /**
     * Get the global default HTTP client
     *
     * @return Client
     */
    protected function getDefaultHttpClient()
    {
        $stack = HandlerStack::create();
        $options = ['handler' => $stack, 'auth' => null];

        if (isset($this->config['debug']) && $this->config['debug'] === true) {
            $options['debug'] = true;
            // Add HTTP-Requests to log
            $stack->push(new GuzzleLogger($this->logger));
        }

        return new Client($options);
    }
}
