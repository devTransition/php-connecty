<?php
/**
 * Unit Test: AbstractResponse class
 */

namespace Connecty\Base\Message;

use Mockery as m;

class AbstractResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractResponse
     */
    private $response;


    public function setUp()
    {
        $this->response = m::mock('\Connecty\Base\Message\AbstractResponse')->makePartial();
    }

    public function testConstruct()
    {
        $data = ['foo' => 'bar'];
        $request = m::mock('\Connecty\Base\Message\RequestInterface');
        $this->response = m::mock('\Connecty\Base\Message\AbstractResponse', [$request, $data])->makePartial();
        $this->assertSame($request, $this->response->getRequest());
        $this->assertSame($data, $this->response->getResponseData());
    }

    public function testDefaultMethods()
    {
        $this->assertNull($this->response->getRequest());
        $this->assertNull($this->response->getResponseData());
    }
}