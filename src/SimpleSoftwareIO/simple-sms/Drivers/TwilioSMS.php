<?php namespace SimpleSoftwareIO\Simple-SMS\Drivers;

use SimpleSoftwareIO\Simple-SMS\Message;
use Services_Twilio;

class TwilioSMS implements DriverInterface {

  /**
   * The Twilio SDK
   *
   * @var Services_Twilio
   */
  protected $twilio;

  /**
   * Creates the TwilioSMS instance.
   *
   * @parma Services_Twilio $twilio The twilio instance.parma
   * @return void
   */
  public function __construct(Services_Twilio $twilio)
  {
    $this->twilio = $twilio;
  }

  /**
   * Sends a SMS message
   *
   * @parma SimpleSoftwareIO\Simple-SMS\Message @messasge The message class.
   * @return void
   */
  public function send(Message $message)
  {
    $from = $message->getFrom();
    $composeMessage = $message->composeMessage($message->getView(), $message->getData());

    foreach ($message->getTo() as $to)
    {
      $this->twilio->account->messages->create([
        'To' => $to['number'],
        'From' => $from,
        'Body' => $composeMessage,
      ]);
    }
  }
}