<?php
/**
 * Connecty gateway interface
 */

namespace Connecty\Base;

/**
 * Connecty gateway interface
 *
 * This interface class defines the standard functions that any Connecty gateway should implement
 *
 * @see AbstractGateway
 */
interface GatewayInterface
{
    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName();

    /**
     * Get gateway alias
     *
     * Alias used with GatewayFactory to create new instances of this gateway.
     */
    public function getAlias();

    /**
     * Define gateway parameters, in the following format:
     *
     * [
     *     'username' => '', // string variable
     *     'testMode' => false, // boolean variable
     *     'landingPage' => array('billing', 'login'), // enum variable, first item is default
     * ];
     */
    public function getDefaultParameters();

    /**
     * Initialize gateway with parameters
     *
     * @param array $params
     */
    public function initialize($params = []);

    /**
     * Get all gateway parameters
     *
     * @return array
     */
    public function getParameters();
}
