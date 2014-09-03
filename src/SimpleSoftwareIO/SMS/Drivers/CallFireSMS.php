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

class CallFireSMS extends AbstractSMS implements DriverInterface
{
    /**
     * The API's URL.
     *
     * @var string
     */
    protected $apiBase = 'https://www.callfire.com/api/1.1/rest';

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
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Sends a SMS message.
     *
     * @param OutgoingMessage $message The SMS message instance.
     * @return void
     */
    public function send(OutgoingMessage $message)
    {
        $composeMessage = $message->composeMessage();

        //Convert to callfire format.
        $numbers = implode(",", $message->getTo());

        $data = [
            'To' => $numbers,
            'Message' => $composeMessage
        ];

        $this->buildCall('/text');
        $this->buildBody($data);

        $this->postRequest();
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
        $incomingMessage->setFrom((string)$rawMessage->FromNumber);
        $incomingMessage->setMessage((string)$rawMessage->TextRecord->Message);
        $incomingMessage->setId((string)$rawMessage['id']);
        $incomingMessage->setTo((string)$rawMessage->ToNumber);

        return $incomingMessage;
    }

    /**
     * Checks the server for messages and returns their results.
     *
     * @param array $options
     * @return array
     */
    public function checkMessages(Array $options = array())
    {
        $this->buildCall('/text');

        $rawMessages = $this->getRequest()->xml();

        return $this->makeMessages($rawMessages->Text);
    }

    /**
     * Gets a single message by it's ID.
     *
     * @param $messageId
     * @return IncomingMessage
     */
    public function getMessage($messageId)
    {
        $this->buildCall('/text/' . $messageId);

        return $this->makeMessage($this->getRequest()->xml()->Text);
    }

    public function receive($raw)
    {
        // TODO: Implement receive() method.  Awaiting CallFire Keyword
    }
}