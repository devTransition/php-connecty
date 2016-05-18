<?php
/**
 * Unit Test: AbstractGateway class
 */

namespace Connecty\Base;

use Connecty\Base\Message\RequestAttributes;
use Mockery as m;
use Connecty\Base\Message\AbstractRequest;
use Psr\Log\LoggerInterface;
use Connecty\Base\Storage\StorageInterface;

class AbstractGatewayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractGatewayTest_MockAbstractGateway
     */
    protected $gateway;

    public function setUp()
    {
        $this->gateway = new AbstractGatewayTest_MockAbstractGateway();
        $this->gateway->initialize();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('\GuzzleHttp\Client', $this->gateway->getProtectedHttpClient());
    }

    public function testGetAlias()
    {
        $this->gateway = m::mock('\Connecty\Base\AbstractGateway')->makePartial();
        $this->assertSame('\\' . get_class($this->gateway), $this->gateway->getAlias());
    }

    public function testLogger()
    {
        $this->assertTrue($this->gateway->logger instanceof LoggerInterface);
    }

    public function testStorage()
    {
        $this->assertNotEmpty($this->gateway->storage);
        $this->assertTrue($this->gateway->storage instanceof StorageInterface);
        $key = 'test_key';
        $value = 'value';
        $this->gateway->storage->set($key, $value);

        $this->assertSame($value, $this->gateway->storage->get($key));
        $this->assertFalse($this->gateway->storage->get('not_existing_key'));
    }

    public function testInitializeParameters()
    {
        $value = 'any_value';
        $this->gateway->initialize([
            'mock_parameter' => $value,
            'unknown' => '42',
        ]);
        $this->assertSame($value, $this->gateway->mock_parameter);
    }

    public function testTestMode()
    {
        $this->gateway->setTestMode(AbstractGateway::TEST_MODE);
        $this->assertSame(AbstractGateway::TEST_MODE, $this->gateway->getTestMode());
    }

    public function testTestModeInitialize()
    {
        $gateway = new AbstractGatewayTest_MockAbstractGateway(['test_mode' => AbstractGateway::SIMULATE_MODE]);
        $this->assertSame(AbstractGateway::SIMULATE_MODE, $gateway->getTestMode());
    }

    public function testCreateRequest()
    {
        $this->gateway = new AbstractGatewayTest_MockAbstractGateway;
        $request = $this->gateway->callCreateRequest(
            '\Connecty\Base\AbstractGatewayTest_MockAbstractRequest', []
        );
        $this->assertNotNull($request->serialize());
    }
}

class AbstractGatewayTest_MockAbstractGateway extends AbstractGateway
{
    public $mock_parameter;

    public function getName()
    {
        return 'Mock Gateway Implementation';
    }

    public function getProtectedHttpClient()
    {
        return $this->http_client;
    }

    public function callCreateRequest($class, $params)
    {
        return $this->createRequest($class, $params);
    }
}

class AbstractGatewayTest_MockAbstractRequest extends AbstractRequest
{
    public function _initializeRequestAttributes()
    {
        $this->request_attributes = new RequestAttributes();
    }

    public function sendRequest($request_data)
    {
    }
}