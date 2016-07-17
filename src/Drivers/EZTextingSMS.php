<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use GuzzleHttp\Client;
use SimpleSoftwareIO\SMS\MakesRequests;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class EZTextingSMS extends AbstractSMS implements DriverInterface
{
    use MakesRequests;

    /**
     * The Guzzle HTTP Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The API's URL.
     *
     * @var string
     */
    protected $apiBase = 'https://app.eztexting.com';

    /**
     * The ending of the URL that all requests must have.
     *
     * @var array
     */
    protected $apiEnding = ['format' => 'json'];

    /**
     * Constructs a new instance.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Sends a SMS message.
     *
     * @param \SimpleSoftwareIO\SMS\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message)
    {
        $composedMessage = $message->composeMessage();

        $data = [
            'PhoneNumbers' => $message->getTo(),
            'Message'      => $composedMessage,
        ];

        $this->buildCall('/sending/messages');
        $this->buildBody($data);

        $this->postRequest();
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
        $this->buildCall('/incoming-messages');
        $this->buildBody($options);

        $rawMessages = $this->getRequest()->json();

        return $this->makeMessages($rawMessages['Response']['Entries']);
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
        $this->buildCall('/incoming-messages');
        $this->buildCall('/'.$messageId);

        $rawMessage = $this->getRequest()->json();

        return $this->makeMessage($rawMessage['Response']['Entry']);
    }

    /**
     * Returns an IncomingMessage object with it's properties filled out.
     *
     * @param $rawMessage
     *
     * @return mixed|\SimpleSoftwareIO\SMS\IncomingMessage
     */
    protected function processReceive($rawMessage)
    {
        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($rawMessage);
        $incomingMessage->setFrom($rawMessage['PhoneNumber']);
        $incomingMessage->setMessage($rawMessage['Message']);
        $incomingMessage->setId($rawMessage['ID']);
        $incomingMessage->setTo('313131');

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
        //Due to the way EZTexting handles Keyword Submits vs Replys
        //We must check both values.
        $from = $raw->get('PhoneNumber') ? $raw->get('PhoneNumber') : $raw->get('from');
        $message = $raw->get('Message') ? $raw->get('Message') : $raw->get('message');

        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($raw->get());
        $incomingMessage->setFrom($from);
        $incomingMessage->setMessage($message);
        $incomingMessage->setTo('313131');

        return $incomingMessage;
    }
}
