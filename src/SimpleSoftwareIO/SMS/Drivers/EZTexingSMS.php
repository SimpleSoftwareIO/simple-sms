<?php namespace SimpleSoftwareIO\SMS\Drivers;

use SimpleSoftwareIO\SMS\Message;
use GuzzleHttp\Client;

class EZTextingSMS implements DriverInterface
{

    /**
     * The Guzzle HTTP Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The EZTexting Username
     *
     * @var string
     */
    protected $username;

    /**
     * The EZTexting Password
     *
     * @var string
     */
    protected $password;

    /**
     * Creates the EZTexting instance.
     *
     * @param $username The username for the API
     * @param $password The password for the API
     * @param Client $client The Guzzle HTTP Client
     */
    public function __construct($username, $password, Client $client)
    {
        $this->username = $username;
        $this->password = $password;
        $this->client = $client;
    }

    /**
     * Sends a SMS message.
     *
     * @param Message $message The SMS message instance.
     * @return void
     */
    public function send(Message $message)
    {
        $composeMessage = $message->composeMessage($message->getView(), $message->getData());

        $data = [
            'User' => $this->username,
            'Password' => $this->password,
            'PhoneNumbers' => $message->getTo(),
            'Message' => $composeMessage
        ];

        $request = $this->client->post($this->getAddress(), ['body' => $data]);
    }

    /**
     * Returns the address of the API.
     *
     * @return string
     */
    protected function getAddress()
    {
        return 'https://app.eztexting.com/sending/messages?format=json';
    }
}