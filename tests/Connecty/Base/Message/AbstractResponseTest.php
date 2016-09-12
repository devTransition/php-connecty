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
        // TODO implement
    }

    public function testDefaultMethods()
    {
        $this->assertNull($this->response->getData());
        $this->assertNull($this->response->getErrors());
        $this->assertNull($this->response->getStatusCode());
    }
}