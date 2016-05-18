<?php
/**
 * Unit Test: GatewayFactory class
 */

namespace Connecty\Base;

use Mockery as m;

class GatewayFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GatewayFactory
     */
    protected $factory;

    public static function setUpBeforeClass()
    {
        m::mock('alias:Connecty\\Tokenizer\\TestGateway');
    }

    public function setUp()
    {
        $this->factory = new GatewayFactory();
    }

    public function testSet()
    {
        $gateways = ['Foo'];
        $this->factory->setGateways($gateways);
        $this->assertSame($gateways, $this->factory->getGateways());
    }

    public function testAddGateway()
    {
        $this->factory->addGateway('Bar');
        $this->assertSame(['Bar'], $this->factory->getGateways());
    }

    public function testAddExistingGateway()
    {
        $this->factory->addGateway('Foo');
        $this->factory->addGateway('Bar');
        $this->factory->addGateway('Bar');
        $this->assertSame(['Foo', 'Bar'], $this->factory->getGateways());
    }

    public function testFindAvailableGateways()
    {
        $this->factory = m::mock('Connecty\Base\GatewayFactory[getSupportedGateways]');
        $this->factory->shouldReceive('getSupportedGateways')->once()
            ->andReturn(['Tokenizer_test']);
        $gateways = $this->factory->find();
        $this->assertContains('Tokenizer_test', $gateways);
        $this->assertContains('Tokenizer_test', $this->factory->getGateways());
    }

    public function testFindIgnoresUnavailableGateways()
    {
        $this->factory = m::mock('Connecty\Base\GatewayFactory[getSupportedGateways]');
        $this->factory->shouldReceive('getSupportedGateways')->once()
            ->andReturn(array('Tokenizer_removed'));
        $gateways = $this->factory->find();
        $this->assertEmpty($gateways);
        $this->assertEmpty($this->factory->getGateways());
    }

    public function testCreateShortName()
    {
        $gateway = $this->factory->create('Tokenizer_test');
        $this->assertInstanceOf('\\Connecty\\Tokenizer\\TestGateway', $gateway);
    }

    public function testCreateFullyQualified()
    {
        $gateway = $this->factory->create('\\Connecty\\Tokenizer\\TestGateway');
        $this->assertInstanceOf('\\Connecty\\Tokenizer\\TestGateway', $gateway);
    }

    /**
     * @expectedException \Connecty\Exception\RuntimeException
     * @expectedExceptionMessage Class '\Connecty\Invalid\Gateway' not found
     */
    public function testCreateInvalid()
    {
        $gateway = $this->factory->create('Invalid');
    }
}