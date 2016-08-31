<?php

namespace Connecty;

use Connecty\Base\Helper;
use Connecty\Base\Log\Logger;
use Connecty\Base\Storage\MemoryStorage;
use Connecty\Base\Storage\StorageInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use Psr\Log\LoggerInterface;

/**
 * The base Connecty class that defines constructor parameters for subclasses
 *
 */
class Connecty
{
    /**
     * Constants definition
     */
    const PRODUCTION_MODE = 0;
    const TEST_MODE = 1;
    const SIMULATE_MODE = 2;
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
     * @var \GuzzleHttp\Client
     */
    protected $http_client;
    /**
     * Configuration
     * @var array
     */
    protected $config;
    /**
     * Test mode integer
     * @var int
     */
    private $test_mode;

    /**
     * Constructor
     *
     * @param array $config
     * @param \GuzzleHttp\Client $http_client - A Guzzle client to make API calls with
     * @param LoggerInterface $logger - the logger that will be used for gateway
     * @param StorageInterface $storage - special cache for storing variables for gateway
     */
    public function __construct(array $config = [], Client $http_client = null, LoggerInterface $logger = null, StorageInterface $storage = null)
    {
        $this->config = $config;

        // initialize default logger with logging disabled if not provided
        $this->logger = $logger !== null ? $logger : new Logger();

        $this->http_client = $http_client ?: $this->getDefaultHttpClient($this->logger);

        // initialize empty memory storage if storage is not provided
        $this->storage = $storage !== null ? $storage : new MemoryStorage();

        $this->test_mode = self::PRODUCTION_MODE;
        if (!empty($config['test_mode'])) {
            $this->setTestMode($config['test_mode']);
        }

        Helper::initialize($this, $config);
    }

    /**
     * Get the global default HTTP client
     *
     * @param LoggerInterface $logger - the logger that will be used for gateway
     * @return Client
     */
    protected function getDefaultHttpClient(LoggerInterface $logger)
    {
        $options = [];

        $client = new Client($options);

        if (isset($this->config['debug']) && $this->config['debug'] === true) {
            // Add HTTP-Requests to log
            $options['debug'] = true;

            // Attach the log channel to the log subscriber of the http client
            $subscriber = new LogSubscriber($logger, $this->getLogFormat());
            $client->getEmitter()->attach($subscriber);
        }

        return $client;
    }

    /**
     * Returns the log format for the http client logger
     *
     * @see https://github.com/guzzle/log-subscriber/blob/master/README.rst
     *
     * @return string
     */
    private function getLogFormat()
    {
        if (!$this->test_mode) {    // production
            return '[{ts}] "{method} {url}" {code}';
        }

        return Formatter::DEBUG;
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
    public function initialize(array $params = [])
    {
        Helper::initialize($this, $params);
        return $this;
    }

}