<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use GuzzleHttp\Client;
use SimpleSoftwareIO\SMS\MakesRequests;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class CallFireSMS extends AbstractSMS implements DriverInterface
{
    use MakesRequests;

    /**
     * The API's URL.
     *
     * @var string
     */
    protected $apiBase = 'https://www.callfire.com/api/1.1/rest';

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
    public function __construct(Client $client, $username, $password)
    {
        $this->client = $client;
        $this->setUser($username);
        $this->setPassword($password);
    }

    /**
     * Sends a SMS message.
     *
     * @param \SimpleSoftwareIO\SMS\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message)
    {
        $composeMessage = $message->composeMessage();

        //Convert to callfire format.
        $numbers = implode(',', $message->getTo());

        $data = [
            'To'      => $numbers,
            'Message' => $composeMessage,
        ];

        $this->buildCall('/text');
        $this->buildBody($data);

        $this->postRequest();
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
        $incomingMessage->setFrom((string) $rawMessage->FromNumber);
        $incomingMessage->setMessage((string) $rawMessage->TextRecord->Message);
        $incomingMessage->setId((string) $rawMessage['id']);
        $incomingMessage->setTo((string) $rawMessage->ToNumber);

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
        $this->buildCall('/text');

        $rawMessages = $this->getRequest()->xml();

        return $this->makeMessages($rawMessages->Text);
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
        $this->buildCall('/text/'.$messageId);

        return $this->makeMessage($this->getRequest()->xml()->Text);
    }

    /**
     * Receives an incoming message via REST call.
     *
     * @param mixed $raw
     *
     * @throws \RuntimeException
     *
     * @return \SimpleSoftwareIO\SMS\IncomingMessage
     */
    public function receive($raw)
    {
        throw new \RuntimeException('CallFire push messages is not supported.');
    }
}
