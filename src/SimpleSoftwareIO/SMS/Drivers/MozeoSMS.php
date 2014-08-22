<?php namespace SimpleSoftwareIO\SMS\Drivers;

use SimpleSoftwareIO\SMS\Message;
use GuzzleHttp\Client;

class MozeoSMS implements DriverInterface
{

    /**
     * The Guzzle HTTP Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The Mozeo API username.
     *
     * @var string
     */
    protected $username;

    /**
     * The Mozeo API password.
     *
     * @var string
     */
    protected $password;

    /**
     * The Mozeo Company Key
     *
     * @var string
     */
    protected $companyKey;

    /**
     * Constructs the MozeoSMS Instance.
     *
     * @param $companyKey Holds the company key
     * @param $username Holds the API username
     * @param $password Holds the API password
     * @param Client $client The guzzle client
     */
    public function __construct($companyKey, $username, $password, Client $client)
    {
        $this->companyKey = $companyKey;
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
        $composeMessage = $message->composeMessage();

        foreach($message->getTo() as $to)
        {
            $data = [
                'companykey' => $this->companyKey,
                'username' => $this->username,
                'password' => $this->password,
                'to' => $to,
                'messagebody' => $composeMessage
            ];

            $request = $this->client->post($this->getAddress(), ['body' => $data]);
        }
    }

    /**
     * Returns the address of the API.
     *
     * @return string
     */
    protected function getAddress()
    {
        return 'https://www.mozeo.com/mozeo/customer/sendtxt-dev.php';
    }
}