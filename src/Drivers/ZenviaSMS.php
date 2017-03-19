<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use GuzzleHttp\Client;
use SimpleSoftwareIO\SMS\MakesRequests;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class ZenviaSMS extends AbstractSMS implements DriverInterface
{
    use MakesRequests;

    /**
     * The API's URL.
     *
     * @var string
     */
    protected $apiBase = 'https://api-rest.zenvia360.com.br/services';

    /**
     * The callbackOption to receive message delivery notifications.
     *
     * @var string
     */
    protected $callbackOption;

    /**
     * The Guzzle HTTP Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * ZenviaSMS constructor.
     *
     * @param Client $client
     * @param $accountKey
     * @param $passCode
     * @param string $callbackOption
     */
    public function __construct(Client $client, $accountKey, $passCode, $callbackOption = 'NONE')
    {
        $this->client = $client;
        $this->setUser($accountKey);
        $this->setPassword($passCode);
        $this->callbackOption = $callbackOption;
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

        $numbers = $message->getToWithCarriers();

        if (count($numbers) > 1) {
            $endpoint = '/send-sms-multiple';
            $data = [
                'sendSmsMultiRequest' => ['sendSmsRequestList' => []],
            ];

            foreach ($numbers as $key => $item) {
                array_push($data['sendSmsMultiRequest']['sendSmsRequestList'],
                    $this->generateMessageBody($from, $item, $composeMessage));
            }
        } else {
            $endpoint = '/send-sms';
            $data = [
                'sendSmsRequest' => $this->generateMessageBody($from, $numbers[0], $composeMessage),
            ];
        }

        $this->buildCall($endpoint);
        $this->buildBody($data);

        $this->postRequest();
    }

    /**
     * Parse a response from messageId check and returns a Message.
     *
     * @param $rawMessage
     *
     * @return \SimpleSoftwareIO\SMS\IncomingMessage
     */
    protected function processReceive($rawMessage)
    {
        $rawMessage = $rawMessage->getSmsStatusResp;
        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($rawMessage);
        $incomingMessage->setFrom($rawMessage->shortcode);
        $incomingMessage->setMessage(null);
        $incomingMessage->setId($rawMessage->id);
        $incomingMessage->setTo(null);

        return $incomingMessage;
    }

    /**
     * Checks the server for messages and returns their results.
     *
     * @param array $options
     *
     * @throws \Exception
     *
     * @return array
     */
    public function checkMessages(array $options = [])
    {
        $this->buildCall('/received/list');

        $this->buildBody($options);

        $jsonResponse = json_decode($this->postRequest()->getBody()->getContents());

        if (! isset($jsonResponse->receivedResponse)) {
            throw new \Exception('Invalid response from API. Missing mandatory object.');
        }

        $rawMessages = $jsonResponse->receivedResponse;

        if ($rawMessages->statusCode !== '00') {
            throw new \Exception(
                'Unable to request from API. Status Code: '.$rawMessages->statusCode
                .' - '.$rawMessages->detailDescription.' ('.$rawMessages->detailCode.')'
            );
        }

        if ($rawMessages->detailCode === '300') {
            return $this->makeMessages($rawMessages->receivedMessages);
        }

        return [];
    }

    /**
     * Gets a single message by it's ID.
     *
     * @param int|string $messageId
     *
     * @return \SimpleSoftwareIO\SMS\IncomingMessage
     */
    public function getMessage($messageId)
    {
        $this->buildCall('/get-sms-status/'.$messageId);

        return $this->makeMessage(json_decode($this->getRequest()->getBody()->getContents()));
    }

    /**
     * Receives an incoming message via REST call.
     *
     * Contact Zenvia Support to get this enabled
     * to your account before using.
     *
     * @param mixed $raw
     *
     * @return \SimpleSoftwareIO\SMS\IncomingMessage
     */
    public function receive($raw)
    {
        $raw = $raw->get('callbackMoRequest');
        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($raw);
        $incomingMessage->setMessage($raw['body']);
        $incomingMessage->setFrom($raw['mobile']);
        $incomingMessage->setId($raw['id']);
        $incomingMessage->setTo($raw['shortCode']);

        return $incomingMessage;
    }

    /**
     * Creates and sends a POST request to the requested URL.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function postRequest()
    {
        $response = $this->client->post($this->buildUrl(),
            [
                'auth'    => $this->getAuth(),
                'json'    => $this->getBody(),
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

        if ($response->getStatusCode() != 201 && $response->getStatusCode() != 200) {
            throw new \Exception('Unable to request from API. HTTP Error: '.$response->getStatusCode());
        }

        return $response;
    }

    /**
     * Creates and sends a GET request to the requested URL.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function getRequest()
    {
        $url = $this->buildUrl($this->getBody());

        $response = $this->client->get($url, [
            'auth'    => $this->getAuth(),
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() != 201 && $response->getStatusCode() != 200) {
            throw new \Exception('Unable to request from API.');
        }

        return $response;
    }

    /**
     * Message body generator based on the attributes.
     *
     * @param $from
     * @param $number
     * @param $composeMessage
     *
     * @return array
     */
    private function generateMessageBody($from, $number, $composeMessage)
    {
        $aux = [
            'from'           => $from,
            'to'             => $number['number'],
            'msg'            => $composeMessage,
            'callbackOption' => $this->callbackOption,
        ];

        if (! is_null($number['carrier'])) {
            $aux['id'] = $number['carrier'];
        }

        return $aux;
    }
}
