<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use SimpleSoftwareIO\SMS\DoesNotReceive;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class JustSendSMS extends AbstractSMS implements DriverInterface
{
    use DoesNotReceive;

    /**
     * The API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Create the JustSendSMS instance.
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Sends a SMS message.
     *
     * @param \SimpleSoftwareIO\SMS\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message)
    {
        $composeMessage = $message->composeMessage();
        $from = $message->getFrom();

        foreach ($message->getTo() as $to) {
            $data = [
                'from'        => $from,
                'bulkVariant' => $this->getVariantForSender($from),
                'message'     => $composeMessage,
                'to'          => $to,
            ];

            $response = $this->sendRequest($data);

            if ($this->hasError($response)) {
                $this->handleError($response);
            }
        }
    }

    /**
     * @param $from
     *
     * @return string
     */
    private function getVariantForSender($from)
    {
        if ($from == 'ECO') {
            return 'ECO';
        } elseif (in_array($from, ['INFO', 'INFORMACJA', 'KONKURS', 'NOWOSC', 'OFERTA', 'OKAZJA', 'PROMOCJA', 'SMS'])) {
            return 'FULL';
        } else {
            return 'PRO';
        }
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    protected function sendRequest($data)
    {
        $curl = curl_init();

        $options = [
            CURLOPT_URL            => "https://justsend.pl/api/rest/message/send/{$this->apiKey}/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => [
                'content-type: application/json',
            ],
        ];
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $this->throwNotSentException('cURL Error #:'.$err);
        }

        return json_decode($response, true);
    }

    /**
     * Checks if the transaction has an error.
     *
     * @param $response
     *
     * @return bool
     */
    protected function hasError($response)
    {
        return $response['responseCode'] != 'OK';
    }

    /**
     * Log the error message which ocurred.
     *
     * @param $response
     */
    protected function handleError($response)
    {
        $message = $response['responseCode'].' ('.$response['errorId'].'): '.$response['message'];
        $this->throwNotSentException($message, $response['errorId']);
    }
}
