<?php

namespace Connecty;


use Connecty\Base\GatewayFactory;

/**
 * The main Connecty class provides static access to the gateway factory methods
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the Tokenizer
 *   // (routes to GatewayFactory::create)
 *   $tokenizer = Connecty::create('Tokenizer');
 *
 *   // Initialise the gateway
 *   $tokenizer->initialize(...);
 *
 *   // Do an authorisation transaction on the gateway
 *   $tokenizer->authorize(...);
 * </code>
 *
 * @see Connecty\Base\GatewayFactory
 */
class Connecty
{
    /**
     * Private factory object, that provides methods
     *
     * @var GatewayFactory
     */
    private static $factory;

    /**
     * Factory getter
     *
     * Creates a new empty factory object if not set
     *
     * @return GatewayFactory $factory
     */
    public static function getFactory()
    {
        if (static::$factory === null) {
            static::$factory = new GatewayFactory();
        }
        return static::$factory;
    }

    /**
     * Factory setter
     *
     * @param GatewayFactory $factory A GatewayFactory instance
     */
    public static function setFactory(GatewayFactory $factory = null)
    {
        static::$factory = $factory;
    }

    /**
     * Static function call router
     *
     * All other function calls to the Connecty class are routed to the factory
     * e.g. Connecty::getSupportedGateways(1, 2, 'test') is routed to the
     * factory's getSupportedGateways method and passed the parameters 1, 2, 'test'
     *
     * Example:
     *
     * <code>
     *   // Create a gateway for Tokenizer
     *   $tokenizer = Connecty::create('Tokenizer');
     * </code>
     *
     * @see GatewayFactory
     *
     * @param string $method - the factory method to call
     * @param array $params - method parameters
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $factory = static::getFactory();
        return call_user_func_array([$factory, $method], $params);
    }
}