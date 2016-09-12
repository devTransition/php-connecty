<?php
/**
 * Unit Test: Connecty class
 */

namespace Connecty;

use Connecty\Base\Message\AbstractRequest;
use Connecty\Base\Message\RequestAttributes;
use Connecty\Base\Model\AbstractModel;
use Connecty\Base\Model\DataModelInterface;
use Connecty\Base\Storage\StorageInterface;
use Mockery as m;
use Psr\Log\LoggerInterface;

class ConnectyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConnectyTest_MockConnecty
     */
    protected $connecty;

    public function setUp()
    {
        $this->connecty = new ConnectyTest_MockConnecty();
        $this->connecty->initialize();
    }

    public function testClassCreation()
    {
        $connecty = new Connecty();
        $this->assertInstanceOf('Connecty\Connecty', $connecty);
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('\GuzzleHttp\Client', $this->connecty->getProtectedHttpClient());
    }

    public function testLogger()
    {
        $this->assertTrue($this->connecty->logger instanceof LoggerInterface);
    }

    public function testStorage()
    {
        $this->assertNotEmpty($this->connecty->storage);
        $this->assertTrue($this->connecty->storage instanceof StorageInterface);
        $key = 'test_key';
        $value = 'value';
        $this->connecty->storage->set($key, $value);

        $this->assertSame($value, $this->connecty->storage->get($key));
        $this->assertFalse($this->connecty->storage->get('not_existing_key'));
    }

    public function testInitializeParameters()
    {
        $value = 'any_value';
        $this->connecty->initialize([
            'mock_parameter' => $value,
            'unknown' => '42',
        ]);
        $this->assertSame($value, $this->connecty->mock_parameter);
    }

    public function testTestMode()
    {
        $this->connecty->setTestMode(Connecty::TEST_MODE);
        $this->assertSame(Connecty::TEST_MODE, $this->connecty->getTestMode());
    }

    public function testTestModeInitialize()
    {
        $connecty = new ConnectyTest_MockConnecty(['test_mode' => Connecty::SIMULATE_MODE]);
        $this->assertSame(Connecty::SIMULATE_MODE, $connecty->getTestMode());
    }

    public function testCreateRequest()
    {
        $this->connecty = new ConnectyTest_MockConnecty;
        $params = ConnecyTest_MockAbstractModel::createFromArray([]);
        $request = $this->connecty->callCreateSampleRequest($params);
        $this->assertNotNull($request->getRequestData());
    }

}


class ConnectyTest_MockConnecty extends Connecty
{
    public $mock_parameter;

    public function getProtectedHttpClient()
    {
        return $this->http_client;
    }

    public function callCreateSampleRequest($params)
    {
        $request_attributes = new RequestAttributes();
        return new ConnecyTest_MockAbstractRequest($this->http_client, $params, $request_attributes);
    }
}

class ConnecyTest_MockAbstractRequest extends AbstractRequest
{
    public function _initializeRequestAttributes()
    {
        $this->request_attributes = new RequestAttributes();
    }

    public function sendRequest(DataModelInterface $request_data)
    {
    }

    public function validate()
    {
        return true;
    }
}

class ConnecyTest_MockAbstractModel extends AbstractModel
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