<?php

use GuzzleHttp\Client;
use SimpleSoftwareIO\SMS\Drivers\SMS77;
use SimpleSoftwareIO\SMS\MakesRequests;
use SimpleSoftwareIO\SMS\OutgoingMessage;
use Mockery as m;

class SMS77Test extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \SimpleSoftwareIO\SMS\SMS
     */
    protected $sms;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $username = getenv('SMS77USER');
        $password = getenv('SMS77PASSWORD');
        $debug = getenv('SMS77DEBUG');
        $this->driver = new SMS77(new GuzzleHttp\Client(), $username, $password, $debug);
        $this->sms = new \SimpleSoftwareIO\SMS\SMS($this->driver);
    }

    public function testSendSMSDebug()
    {
        $viewFactory = m::mock('\Illuminate\View\Factory');
        $view = m::mock('\Illuminate\View\View');
        $viewFactory->shouldReceive('make')->andReturn($view);
        $view->shouldReceive('render')->andReturn('Hello world');

        $message = new OutgoingMessage($viewFactory);
        $message->view($viewFactory);
        $message->data([]);
        $message->to('+155555555');
        $this->driver->send($message);
    }



    public function testSendSMSReal()
    {
        $this->markTestSkipped('Not sending real SMS - comment this line to try');
    }
}