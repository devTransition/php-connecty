<?php
/**
 * Unit Test: Connecty class
 */

namespace Connecty;

use Mockery as m;

class ConnectyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Clean the tested objects between tests
     */
    public function tearDown()
    {
        Connecty::setFactory(null);
    }

    public function testGetFactory()
    {
        Connecty::setFactory(null);
        $factory = Connecty::getFactory();
        $this->assertInstanceOf('Connecty\Base\GatewayFactory', $factory);
    }

    public function testSetFactory()
    {
        $factory = m::mock('Connecty\Base\GatewayFactory');
        Connecty::setFactory($factory);
        $this->assertSame($factory, Connecty::getFactory());
    }
    
    public function testCallStatic()
    {
        $factory = m::mock('Connecty\Base\GatewayFactory');
        $factory->shouldReceive('testMethod')->with('some-argument')->once()->andReturn('some-result');
        Connecty::setFactory($factory);
        $result = Connecty::testMethod('some-argument');
        $this->assertSame('some-result', $result);
    }
}