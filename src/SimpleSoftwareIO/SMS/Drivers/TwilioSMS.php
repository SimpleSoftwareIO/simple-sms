<?php namespace SimpleSoftwareIO\SMS\Drivers;

/**
 * Simple-SMS
 * A simple SMS message sendingn for Laravel.
 *
 * @link http://www.simplesoftware.io
 * @author SimpleSoftware support@simplesoftware.io
 *
 */

use SimpleSoftwareIO\SMS\OutgoingMessage;
use Services_Twilio;

class TwilioSMS implements DriverInterface
{

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
     * @parma SimpleSoftwareIO\SMS\OutgoingMessage @messasge The message class.
     * @return void
     */
    public function send(OutgoingMessage $message)
    {
        $from = $message->getFrom();
        $composeMessage = $message->composeMessage();

        foreach ($message->getTo() as $to) {
            $this->twilio->account->messages->create([
                'To' => $to,
                'From' => $from,
                'Body' => $composeMessage,
            ]);
        }
    }
}