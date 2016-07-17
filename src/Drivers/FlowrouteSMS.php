<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use GuzzleHttp\Client;
use SimpleSoftwareIO\SMS\MakesRequests;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class FlowrouteSMS extends AbstractSMS implements DriverInterface
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
    protected $apiBase = 'https://api.flowroute.com/v2/messages';

    /**
     * Create the Flowroute instance.
     *
     * @param Client $client The Guzzle Client
     */
    public function __construct(Client $client, $accessKey, $secretKey)
    {
        $this->client = $client;
        $this->setUser($accessKey);
        $this->setPassword($secretKey);
    }

    /**
     * Sends a SMS message.
     *
     * @param \SimpleSoftwareIO\SMS\OutgoingMessage $message
     *
     * @return void
     */
    public function send(OutgoingMessage $message)
    {
        $from = $message->getFrom();
        $composeMessage = $message->composeMessage();

        foreach ($message->getTo() as $number) {
            $data = [
                'from'          => $from,
                'to'            => $number,
                'body'          => $composeMessage,
            ];

            $this->buildBody($data);
            $this->postRequest();
        }
    }

    /**
     * Checks the server for messages and returns their results.
     * See https://developer.flowroute.com/docs/lookup-a-set-of-messages.
     *
     * @param array $options
     *
     * @return array
     */
    public function checkMessages(array $options = [])
    {
        $this->buildBody($options);

        $rawMessages = json_decode($this->getRequest()->getBody()->getContents());

        return $this->makeMessages($rawMessages->data);
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
        $this->buildCall('/'.$messageId);

        return $this->makeMessage(json_decode($this->getRequest()->getBody()->getContents())->data);
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
        $incomingMessage->setMessage($raw->get('body'));
        $incomingMessage->setFrom($raw->get('from'));
        $incomingMessage->setId($raw->get('id'));
        $incomingMessage->setTo($raw->get('to'));

        return $incomingMessage;
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
        $incomingMessage->setFrom((string) $rawMessage->attributes->from);
        $incomingMessage->setMessage((string) $rawMessage->attributes->body);
        $incomingMessage->setId((string) $rawMessage->id);
        $incomingMessage->setTo((string) $rawMessage->attributes->to);

        return $incomingMessage;
    }

    /**
     * Creates and sends a POST request to the requested URL.
     *
     * @return mixed
     */
    protected function postRequest()
    {
        $response = $this->client->post($this->buildUrl(), [
            'auth' => $this->getAuth(),
            'json' => $this->getBody(),
        ]);

        if ($response->getStatusCode() != 201 && $response->getStatusCode() != 200) {
            $this->SMSNotSentException('Unable to request from API.');
        }

        return $response;
    }
}
