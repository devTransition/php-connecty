<?php
/**
 * Unit Test: AbstractRequest class
 */

namespace Connecty\Base\Message;

use Mockery as m;
use Guzzlehttp\Client as Client;

class AbstractRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractRequestTest_MockAbstractRequest
     */
    private $request;
    /**
     * @var Client
     */
    private $http_client;

    public function setUp()
    {
        $this->request = m::mock('\Connecty\Base\Message\AbstractRequestTest_MockAbstractRequest')->makePartial();
        $this->request->initialize();
    }

    public function getHttpClient()
    {
        if (!$this->http_client) {
            $this->http_client = new Client();
        }
        return $this->http_client;
    }

    public function testConstruct()
    {
        $this->request = new AbstractRequestTest_MockAbstractRequest($this->getHttpClient(), null);
        $this->assertSame(['amount', 'currency'], $this->request->getParameters());
    }

    public function testInitializeWithParams()
    {
        $this->assertSame($this->request, $this->request->initialize(['amount' => '1.23']));
        $this->assertSame('1.23', $this->request->amount);
    }

    /**
     * @expectedException \Connecty\Exception\RuntimeException
     * @expectedExceptionMessage Request cannot be modified after it has been sent
     */
    public function testInitializeAfterRequestSent()
    {
        $this->request = new AbstractRequestTest_MockAbstractRequest($this->getHttpClient(), ['amount' => 1, 'currency' => 'EUR']);
        $this->request->send();
        $this->request->initialize();
    }

    public function testAmount()
    {
        $this->request->amount = '1.00';
        $this->assertSame('1.00', $this->request->amount);
    }

    public function testCurrency()
    {
        $this->request->currency = 'EUR';
        $this->assertSame('EUR', $this->request->currency);
    }

    public function testGetParameters()
    {
        $expected = ['amount', 'currency',];
        $this->assertEquals($expected, $this->request->getParameters());
    }

    public function testSerialize()
    {
        $this->request->currency = 'EUR';
        $this->request->amount = 'asdf';
        $expected = ['currency' => 'EUR', 'amount' => 'asdf',];
        $this->assertEquals($expected, $this->request->serialize());
    }

    public function testCanValidateExistingParameters()
    {
        $this->assertFalse($this->request->validate());
    }

    /**
     * @expectedException \Connecty\Exception\InvalidArgumentException
     * @expectedExceptionMessage Request Mockery_0_Connecty_Base_Message_AbstractRequestTest_MockAbstractRequest is not valid
     */
    public function testSend()
    {
        $response = m::mock('\Connecty\Base\Message\ResponseInterface');
        $data = ['request data'];
        $this->request->shouldReceive('serialize')->once()->andReturn($data);
        $this->request->shouldReceive('sendRequest')->once()->with($data)->andReturn($response);
        $this->assertSame($response, $this->request->send());
    }

    /**
     * @expectedException \Connecty\Exception\RuntimeException
     * @expectedExceptionMessage You must call send() before accessing the Response
     */
    public function testGetResponseBeforeRequestSent()
    {
        $this->request = new AbstractRequestTest_MockAbstractRequest($this->getHttpClient());
        $this->request->getResponse();
    }

    public function testGetResponseAfterRequestSent()
    {
        $this->request = new AbstractRequestTest_MockAbstractRequest($this->getHttpClient(), ['amount' => 1, 'currency' => 'EUR']);
        $this->request->send();
        $response = $this->request->getResponse();
        $this->assertInstanceOf('\Connecty\Base\Message\ResponseInterface', $response);
    }
}

class AbstractRequestTest_MockAbstractRequest extends AbstractRequest
{
    public $amount;
    public $currency;

    public function validate()
    {
        return !empty($this->amount) && !empty($this->currency);
    }

    public function _initializeRequestAttributes()
    {
        $this->request_attributes = new RequestAttributes();
    }

    public function sendRequest($request_data)
    {
        $this->response = m::mock('\Connecty\Base\Message\AbstractResponse');
    }
}