<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use GuzzleHttp\Client;
use SimpleSoftwareIO\SMS\MakesRequests;
use SimpleSoftwareIO\SMS\DoesNotReceive;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class LabsMobileSMS extends AbstractSMS implements DriverInterface
{
    use DoesNotReceive, MakesRequests;

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
    protected $apiBase = 'https://api.labsmobile.com/get/send.php';

    /**
     * Constructs the MozeoSMS Instance.
     *
     * @param Client $client The guzzle client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Sends a SMS message.
     *
     * @param OutgoingMessage $message The SMS message instance.
     */
    public function send(OutgoingMessage $message)
    {
        $composeMessage = $message->composeMessage();

        foreach ($message->getTo() as $to) {
            $data = [
                'msisdn'  => $to,
                'message' => $composeMessage,
            ];

            $this->buildBody($data);

            $this->getRequest();
        }
    }
}
