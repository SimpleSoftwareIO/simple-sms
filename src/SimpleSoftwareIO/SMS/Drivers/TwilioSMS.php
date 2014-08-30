<?php namespace SimpleSoftwareIO\SMS\Drivers;

/**
 * Simple-SMS
 * A simple SMS message sending for Laravel.
 *
 * @link http://www.simplesoftware.io
 * @author SimpleSoftware support@simplesoftware.io
 *
 */

use SimpleSoftwareIO\SMS\IncomingMessage;
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

    public function __construct(Services_Twilio $twilio)
    {
        $this->twilio = $twilio;
    }

    /**
     * Sends a SMS message.
     *
     * @param OutgoingMessage $message The OutgoingMessage instance.
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

    /**
     * Processing the raw information from a request and inputs it into the IncomingMessage object.
     *
     * @param IncomingMessage $incomingMessage
     * @param $raw
     * @return void
     */
    protected function processReceive(IncomingMessage $incomingMessage, $raw)
    {
        $incomingMessage->setRaw($raw);
        $incomingMessage->setMessage($raw->body);
        $incomingMessage->setFrom($raw->from);
        $incomingMessage->setId($raw->sid);
        $incomingMessage->setTo($raw->to);
    }

    /**
     * Returns an array full of IncomingMessage objects.
     *
     * @param array $options The options of filters to pass onto twilio.  Options are To, From, and DateSent
     * @return array
     */
    public function checkMessages(Array $options = array())
    {
        $start = array_key_exists('start', $options) ? $options['start'] : 0;
        $end = array_key_exists('end', $options) ? $options['end'] : 25;

        $rawMessages = $this->twilio->account->messages->getIterator($start, $end, $options);

        foreach ($rawMessages as $rawMessage)
        {
            $incomingMessage = $this->createIncomingMessage();
            $this->processReceive($incomingMessage, $rawMessage);
            $incomingMessages[] = $incomingMessage;
        }

        return $incomingMessages;
    }

    /**
     * Gets a message by messageId.
     *
     * @param $messageId The requested messageId.
     * @return IncomingMessage
     */
    public function getMessage($messageId)
    {
        $rawMessage = $this->twilio->account->messages->get($messageId);
        $incomingMessage = $this->createIncomingMessage();
        $this->processReceive($incomingMessage, $rawMessage);
        return $incomingMessage;
    }

    /**
     * Creates a new IncomingMessage isntance.
     *
     * @return IncomingMessage
     */
    protected function createIncomingMessage()
    {
        return new IncomingMessage();
    }

    /**
     * Push receives.  This method will take a request and convert it into an IncomingMessage object.
     *
     * @param $raw The raw data.
     * @return IncomingMessage
     */
    public function receive($raw)
    {
        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($raw->get());
        $incomingMessage->setMessage($raw->get('Body'));
        $incomingMessage->setFrom($raw->get('From'));
        $incomingMessage->setId($raw->get('MessageSid'));
        $incomingMessage->setTo($raw->get('To'));
        return $incomingMessage;
    }
}