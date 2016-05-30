<?php

use Mockery as m;
use SimpleSoftwareIO\SMS\SMS;

class SMSTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $this->sms = new SMS(m::mock('SimpleSoftwareIO\SMS\Drivers\DriverInterface'));
    }
}
