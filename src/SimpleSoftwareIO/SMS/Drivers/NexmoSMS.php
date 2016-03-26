<?php namespace SimpleSoftwareIO\SMS\Drivers;

/**
 * Simple-SMS
 * Simple-SMS is a package made for Laravel to send/receive (polling/pushing) text messages.
 *
 * @link http://www.simplesoftware.io
 * @author SimpleSoftware support@simplesoftware.io
 *
 */

use SimpleSoftwareIO\SMS\OutgoingMessage;
use GuzzleHttp\Client;

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
     * The Guzzle HTTP Client
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
     * @return void
     */
    public function send(OutgoingMessage $message)
    {
        $from = $message->getFrom();
        $composeMessage = $message->composeMessage();

        //Convert to callfire format.
        $numbers = implode(",", $message->getTo());

        $data = [
            'from'          => $from,
            'to'            => $numbers,
            'text'          => $composeMessage,
            'api_key'       => $this->apiKey,
            'api_secret'    => $this->apiSecret,
        ];

        $this->buildCall('/sms/json');
        $this->buildBody($data);

        $response = $this->postRequest();
        $body = json_decode($response->getBody(), true);
        if ($this->hasError($body)) {
            $this->handleError($body);
        }
        
        return $response;
    }
    
    /**
     * Checks if the transaction has an error
     *
     * @param $body
     * @return bool
     */
    protected function hasError($body)
    {
        if ($this->hasAResponseMessage($body) && $this->hasProperty($this->getFirstMessage($body), 'status')) {
            $firstMessage = $this->getFirstMessage($body);
            return (int)$firstMessage['status'] !== 0;
        }
        return false;
    }
    
    /**
     * Log the error message which ocurred
     *
     * @param $body
     */
    protected function handleError($body)
    {
        $firstMessage = $this->getFirstMessage($body);
        $error = 'An error occurred. Nexmo status code: ' . $firstMessage['status'];
        if ($this->hasProperty($firstMessage, 'error-text')) {
            $error = $firstMessage['error-text'];
        }
        
        \Log::error($error);
        $this->throwNotSentException($error, $firstMessage['status']);
    }
    
    /**
     * Check for a message in the response from Nexmo
     *
     * @param $body
     */
    protected function hasAResponseMessage($body)
    {
        return (
            is_array($body) &&
            array_key_exists('messages', $body) &&
            array_key_exists(0, $body['messages'])
        );
    }
    
    /**
     * Get the first message in the response from Nexmo
     *
     * @param $body
     */
    protected function getFirstMessage($body)
    {
        return $body['messages'][0];
    }
    
    /**
     * Check if the message from Nexmo has a given property
     *
     * @param $message
     * @param $property
     * @return bool
     */
    protected function hasProperty($message, $property)
    {
        return array_key_exists($property, $message);
    }

    /**
     * Creates many IncomingMessage objects and sets all of the properties.
     *
     * @param $rawMessage
     * @return mixed
     */
    protected function processReceive($rawMessage)
    {
        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($rawMessage);
        $incomingMessage->setFrom((string)$rawMessage->from);
        $incomingMessage->setMessage((string)$rawMessage->body);
        $incomingMessage->setId((string)$rawMessage->{'message-id'});
        $incomingMessage->setTo((string)$rawMessage->to);

        return $incomingMessage;
    }

    /**
     * Checks the server for messages and returns their results.
     *
     * @param array $options
     * @return array
     */
    public function checkMessages(array $options = [])
    {
        $this->buildCall('/search/messages/' . $this->apiKey . '/' . $this->apiSecret);

        $this->buildBody($options);

        $rawMessages = json_decode($this->getRequest()->getBody()->getContents());

        return $this->makeMessages($rawMessages->items);
    }

    /**
     * Gets a single message by it's ID.
     *
     * @param string|int $messageId
     * @return \SimpleSoftwareIO\SMS\IncomingMessage
     */
    public function getMessage($messageId)
    {
        $this->buildCall('/search/message/' . $this->apiKey . '/' . $this->apiSecret . '/' . $messageId);

        return $this->makeMessage(json_decode($this->getRequest()->getBody()->getContents()));
    }

    /**
     * Receives an incoming message via REST call.
     *
     * @param mixed $raw
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
