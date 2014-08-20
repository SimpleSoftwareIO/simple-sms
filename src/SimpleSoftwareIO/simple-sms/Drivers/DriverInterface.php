<?php namespace SimpleSoftwareIO\Simple-SMS\Drivers;

use SimpleSoftwareIO\Simple-SMS\Message;

interface DriverInterface {
  /**
   * Sends a SMS message
   *
   * @parma SimpleSoftwareIO\Simple-SMS\Message @messasge The message class.
   * @return void
   */
  public function send(Message $message);
}