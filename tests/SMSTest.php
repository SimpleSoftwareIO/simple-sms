<?php

/**
 * Simple-SMS
 * Simple-SMS is a package made for Laravel to send/receive (polling/pushing) text messages.
 *
 * @link http://www.simplesoftware.io
 * @author SimpleSoftware support@simplesoftware.io
 *
 */

use Mockery as m;
use SimpleSoftwareIO\SMS\SMS;

class SMSTest extends \PHPUnit_Framework_TestCase {

  public function tearDown()
  {
    m::close();
  }

  public function setUp()
  {
    $this->sms = new SMS(m::mock('SimpleSoftwareIO\SMS\Drivers\DriverInterface'));
  }

  public function testIsPretendingByDefault()
  {
    $this->assertFalse($this->sms->isPretending());
  }

  public function testPretendIsSet()
  {
    $this->sms->setPretending(true);

    $this->assertTrue($this->sms->isPretending());
  }
}