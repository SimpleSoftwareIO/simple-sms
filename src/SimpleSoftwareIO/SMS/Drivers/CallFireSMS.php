<?php namespace SimpleSoftwareIO\SMS\Drivers;

use SimpleSoftwareIO\SMS\Message;
use GuzzleHttp\Client;

class CallFireSMS implements DriverInterface
{

    /**
     * The Guzzle HTTP Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The CallFire API Login.
     *
     * @var string
     */
    protected $app_login;

    /**
     * The CalLFire API password.
     *
     * @var string
     */
    protected $app_password;

    /**
     * Create the CallFire instance.
     *
     * @param $app_login The API login
     * @param $app_password The API password
     * @param Client $client The Guzzle Client
     */
    public function __construct($app_login, $app_password, Client $client)
    {
        $this->app_login = $app_login;
        $this->app_password = $app_password;
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
        $composeMessage = $message->composeMessage();

        $numbers = implode(",", $message->getTo());

        $data = [
            'To' => $numbers,
            'Message' => $composeMessage
        ];

        $request = $this->client->post($this->getAddress(), ['auth' => [$this->username, $this->password], 'body' => $data]);
    }

    /**
     * Returns the address of the API.
     *
     * @return string
     */
    protected function getAddress()
    {
        return 'https://www.callfire.com/api/1.1/rest/text';
    }
}