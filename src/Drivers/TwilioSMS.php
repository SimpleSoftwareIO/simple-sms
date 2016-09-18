<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use Services_Twilio;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class TwilioSMS extends AbstractSMS implements DriverInterface
{
    /**
     * The Twilio SDK.
     *
     * @var Services_Twilio
     */
    protected $twilio;

    /**
     * Holds the Twilio auth token.
     *
     * @var string
     */
    protected $authToken;

    /**
     * Holds the request URL to verify a Twilio push.
     *
     * @var string
     */
    protected $url;

    /**
     * Determines if requests should be checked to be authentic.
     *
     * @var bool
     */
    protected $verify;

    /**
     * Constructs the TwilioSMS object.
     *
     * @param Services_Twilio $twilio
     * @param $authToken
     * @param $url
     * @param bool $verify
     */
    public function __construct(Services_Twilio $twilio, $authToken, $url, $verify = false)
    {
        $this->twilio = $twilio;
        $this->authToken = $authToken;
        $this->url = $url;
        $this->verify = $verify;
    }

    /**
     * Sends a SMS message.
     *
     * @param \SimpleSoftwareIO\SMS\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message)
    {
        $from = $message->getFrom();
        $composeMessage = $message->composeMessage();

        foreach ($message->getTo() as $to) {
            $this->twilio->account->messages->create([
                'To'       => $to,
                'From'     => $from,
                'Body'     => $composeMessage,
                'MediaUrl' => $message->getAttachImages(),
            ]);
        }
    }

    /**
     * Processing the raw information from a request and inputs it into the IncomingMessage object.
     *
     * @param $raw
     */
    protected function processReceive($raw)
    {
        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($raw);
        $incomingMessage->setMessage($raw->body);
        $incomingMessage->setFrom($raw->from);
        $incomingMessage->setId($raw->sid);
        $incomingMessage->setTo($raw->to);
    }

    /**
     * Checks the server for messages and returns their results.
     *
     * @param array $options
     *
     * @return array
     */
    public function checkMessages(array $options = [])
    {
        $start = array_key_exists('start', $options) ? $options['start'] : 0;
        $end = array_key_exists('end', $options) ? $options['end'] : 25;

        $rawMessages = $this->twilio->account->messages->getIterator($start, $end, $options);
        $incomingMessages = [];

        foreach ($rawMessages as $rawMessage) {
            $incomingMessage = $this->createIncomingMessage();
            $this->processReceive($incomingMessage, $rawMessage);
            $incomingMessages[] = $incomingMessage;
        }

        return $incomingMessages;
    }

    /**
     * Gets a single message by it's ID.
     *
     * @param string|int $messageId
     *
     * @return \SimpleSoftwareIO\SMS\IncomingMessage
     */
    public function getMessage($messageId)
    {
        $rawMessage = $this->twilio->account->messages->get($messageId);
        $incomingMessage = $this->createIncomingMessage();
        $this->processReceive($incomingMessage, $rawMessage);

        return $incomingMessage;
    }

    /**
     * Receives an incoming message via REST call.
     *
     * @param mixed $raw
     *
     * @return \SimpleSoftwareIO\SMS\IncomingMessage
     */
    public function receive($raw)
    {
        if ($this->verify) {
            $this->validateRequest();
        }

        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($raw->get());
        $incomingMessage->setMessage($raw->get('Body'));
        $incomingMessage->setFrom($raw->get('From'));
        $incomingMessage->setId($raw->get('MessageSid'));
        $incomingMessage->setTo($raw->get('To'));

        return $incomingMessage;
    }

    /**
     * Checks if a message is authentic from Twilio.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateRequest()
    {
        //Twilio requires that all POST data be sorted alpha to validate.
        $data = $_POST;
        ksort($data);

        // append the data array to the url string, with no delimiters
        $url = $this->url;
        foreach ($data as $key => $value) {
            $url = $url.$key.$value;
        }

        //Encode the request string
        $hmac = hash_hmac('sha1', $url, $this->authToken, true);

        //Verify it against the given Twilio key
        if (base64_encode($hmac) != $_SERVER['HTTP_X_TWILIO_SIGNATURE']) {
            throw new \InvalidArgumentException('This request was not able to verify it came from Twilio.');
        }
    }
}
