<?php
/**
 * Unit Test: AbstractRequest class
 */

namespace Connecty\Base\Message;

use Connecty\Base\Model\AbstractModel;
use Connecty\Base\Model\RequestDataInterface;
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
        $this->assertSame($this->request, $this->request->initialize(AbstractRequestTest_MockAbstractModel::createFromArray(['amount' => '1.23'])));
        $this->assertSame('1.23', $this->request->);
    }

    /**
     * @expectedException \Connecty\Exception\RuntimeException
     * @expectedExceptionMessage Request cannot be modified after it has been sent
     */
    public function testInitializeAfterRequestSent()
    {
        $request_data = AbstractRequestTest_MockAbstractModel::createFromArray(['amount' => 1, 'currency' => 'EUR']);

        $this->request = new AbstractRequestTest_MockAbstractRequest($this->getHttpClient(), $request_data);
        $this->request->send();
        $this->request->initialize(AbstractRequestTest_MockAbstractModel::createFromArray(['amount' => 99, 'currency' => 'EUR']));
    }

    public function testAmount()        // TODO this test validate only php-functionality
    {
        $this->request->amount = '1.00';
        $this->assertSame('1.00', $this->request->amount);
    }

    public function testCurrency()      // TODO this test validate only php-functionality
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
        $request_data = AbstractRequestTest_MockAbstractModel::createFromArray(['amount' => 1, 'currency' => 'EUR']);

        $this->request = new AbstractRequestTest_MockAbstractRequest($this->getHttpClient(), $request_data);
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
        if (empty($this->request_data)) {
            return false;
        }

        $arr = $this->request_data->toArray();

        if (empty($arr)) {
            return false;
        }

        return !empty($arr['amount']) && !empty($arr['currency']);
    }

    public function _initializeRequestAttributes()
    {
        $this->request_attributes = new RequestAttributes();
    }

    public function sendRequest(RequestDataInterface $request_data)
    {
        $this->response = m::mock('\Connecty\Base\Message\AbstractResponse');
    }
}

class AbstractRequestTest_MockAbstractModel extends AbstractModel
{
    protected $amount;

    protected $currency;

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }
}