<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use GuzzleHttp\Client;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class NexmoSMS extends AbstractSMS implements DriverInterface
{
    /**
     * The API's URL.
     *
     * @var string
     */
    protected $apiBase = 'https://rest.nexmo.com';

    protected $apiKey;
    protected $apiSecret;

    /**
     * The Guzzle HTTP Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Create the CallFire instance.
     *
     * @param Client $client The Guzzle Client
     */
    public function __construct(Client $client, $apiKey, $apiSecret)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
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

        //Convert to callfire format.
        $numbers = implode(',', $message->getTo());

        $data = [
            'from' => $from,
            'to' => $numbers,
            'text' => $composeMessage,
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
        ];

        $this->buildCall('/sms/json');
        $this->buildBody($data);

        return $this->postRequest();
    }

    /**
     * Creates many IncomingMessage objects and sets all of the properties.
     *
     * @param $rawMessage
     *
     * @return mixed
     */
    protected function processReceive($rawMessage)
    {
        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($rawMessage);
        $incomingMessage->setFrom((string) $rawMessage->from);
        $incomingMessage->setMessage((string) $rawMessage->body);
        $incomingMessage->setId((string) $rawMessage->{'message-id'});
        $incomingMessage->setTo((string) $rawMessage->to);

        return $incomingMessage;
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
        $this->buildCall('/search/messages/'.$this->apiKey.'/'.$this->apiSecret);

        $this->buildBody($options);

        $rawMessages = json_decode($this->getRequest()->getBody()->getContents());

        return $this->makeMessages($rawMessages->items);
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
        $this->buildCall('/search/message/'.$this->apiKey.'/'.$this->apiSecret.'/'.$messageId);

        return $this->makeMessage(json_decode($this->getRequest()->getBody()->getContents()));
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
        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($raw->get());
        $incomingMessage->setMessage($raw->get('text'));
        $incomingMessage->setFrom($raw->get('msisdn'));
        $incomingMessage->setId($raw->get('messageId'));
        $incomingMessage->setTo($raw->get('to'));

        return $incomingMessage;
    }
}
