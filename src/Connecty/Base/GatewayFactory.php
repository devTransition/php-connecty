<?php
/**
 * Gateway Factory class file
 */

namespace Connecty\Base;

use GuzzleHttp\Client;
use Connecty\Exception\ConnectyException;
use Connecty\Exception\RuntimeException;

/**
 * Gateway Factory class works with a set of gateways that can be independently
 * registered, accessed, and used
 *
 * Note that static calls to the Connecty class are routed to this class by
 * the static call router (__callStatic) in Connecty class
 *
 * Example:
 *
 * <code>
 *   // Create a gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Connecty::create('TokenizerGateway');
 * </code>
 *
 * @see Connecty\Connecty
 */
class GatewayFactory
{
    /**
     * Storage for all available gateways
     *
     * @var array
     */
    private $gateways = [];

    /**
     * Gateways getter
     *
     * @return array An array of gateway names
     */
    public function getGateways()
    {
        return $this->gateways;
    }

    /**
     * Gateways setter
     *
     * @param array $value An array of gateway names
     */
    public function setGateways($value)
    {
        $this->gateways = $value;
    }

    /**
     * Add a new gateway
     *
     * @param string $gateway_name
     */
    public function addGateway($gateway_name)
    {
        if (!in_array($gateway_name, $this->gateways)) {
            $this->gateways[] = $gateway_name;
        }
    }

    /**
     * Automatically find and register all supported gateways
     *
     * @return array An array of gateway names
     */
    public function find()
    {
        foreach ($this->getSupportedGateways() as $gateway) {
            $gateway_class = Helper::getGatewayClassName($gateway);
            if (class_exists($gateway_class)) {
                $this->addGateway($gateway);
            }
        }

        ksort($this->gateways);

        return $this->getGateways();
    }

    /**
     * Create a new gateway instance
     *
     * @param string $gateway_name
     * @param array $config
     * @param Client $http_client - A Guzzle HTTP Client implementation
     * @param LoggerInterface $logger - for logging inside gateway (default std_out)
     * @param StorageInterface $storage - for caching
     * @throws RuntimeException - If no such gateway is found
     * @return GatewayInterface object of class $gateway_name
     */
    public function create($gateway_name, $config = [], Client $http_client = null, LoggerInterface $logger = null, StorageInterface $storage = null)
    {
        $gateway_name = Helper::getGatewayClassName($gateway_name);

        if (!class_exists($gateway_name)) {
            throw new RuntimeException("Class '$gateway_name' not found");
        }

        return new $gateway_name($config, $http_client, $logger, $storage);
    }

    /**
     * Get a list of supported gateways which may be available
     *
     * @return array
     * @throws RuntimeException
     */
    public function getSupportedGateways()
    {
        $package = json_decode(file_get_contents(__DIR__ . '/../../../composer.json'), true);

        if (empty($package['extra'])) {
            throw new RuntimeException('Cannot find supported gateways in the composer.json file');
        }

        return $package['extra']['gateways'];
    }
}
