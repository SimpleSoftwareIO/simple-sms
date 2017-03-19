<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use GuzzleHttp\Client;
use SimpleSoftwareIO\SMS\MakesRequests;
use SimpleSoftwareIO\SMS\DoesNotReceive;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class SMS77 extends AbstractSMS implements DriverInterface
{
    use DoesNotReceive, MakesRequests;

    /**
     * The API's URL.
     *
     * @var string
     */
    protected $apiBase = 'http://gateway.sms77.de/';

    /**
     * The Guzzle HTTP Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Create the SMS77 instance.
     *
     * @param Client $client The Guzzle Client
     */
    public function __construct(Client $client, $username, $password, $debug = false)
    {
        $this->client = $client;
        $this->setUser($username);
        $this->setPassword($password);
        $this->setDebug($debug);
    }

    /**
     * Sends a SMS message.
     *
     * @param \SimpleSoftwareIO\SMS\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message)
    {
        $composeMessage = $message->composeMessage();

        //Convert to sms77 format.
        $numbers = implode(',', $message->getTo());

        $data = [
            'u' => $this->auth['username'],
            'p' => $this->auth['password'],
            'to' => $numbers,
            'from' => $message->getFrom(),
            'type' => 'direct',
            'text' => $composeMessage,
            'debug' => (int) $this->debug,
        ];

        $this->buildCall('');
        $this->buildBody($data);

        $response = $this->postRequest();
        $responseBody = $response->getBody()->read(3);
        if ($this->hasError($responseBody)) {
            $this->handleError($responseBody);
        }

        return $response;
    }

    /**
     * Checks if the transaction has an error.
     *
     * @param $body
     * @return bool
     */
    protected function hasError($body)
    {
        if ($body != '100') {
            return $body;
        }

        return false;
    }

    /**
     * Log the error message which ocurred.
     *
     * @param $body
     */
    protected function handleError($body)
    {
        $error = 'An error occurred. Status code: '.$body.' - ';

        //From https://www.sms77.de/api.pdf Rückgabewerte (German doc)
        switch ($body) {
            case '101':
                $error .= 'Versand an mindestens einen Empfänger fehlgeschlagen';
                break;
            case '201':
                $error .= 'Absender ungültig. Erlaubt sind max 11 alphanumerische oder 16 numerische Zeichen.';
                break;
            case '202':
                $error .= 'Empfängernummer ungültig';
                break;
            case '300':
                $error .= 'Bitte Benutzer/Passwort angeben';
                break;
            case '301':
                $error .= 'Variable to nicht gesetzt';
                break;
            case '304':
                $error .= 'Variable type nicht gesetzt';
                break;
            case '305':
                $error .= 'Variable text nicht gesetzt';
                break;
            case '306':
                $error .= 'Absendernummer ungültig (nur bei Standard SMS). Diese muss vom Format 0049... sein un eine gültige Handynummer darstellen.';
                break;
            case '307':
                $error .= 'Variable url nicht gesetzt';
                break;
            case '400':
                $error .= 'type ungültig. Siehe erlaubte Werte oben.';
                break;
            case '401':
                $error .= 'Variable text ist zu lang';
                break;
            case '402':
                $error .= 'Reloadsperre – diese SMS wurde bereits innerhalb der letzten 90 Sekunden verschickt';
                break;
            case '500':
                $error .= 'Zu wenig Guthaben vorhanden.';
                break;
            case '600':
                $error .= 'Carrier Zustellung misslungen';
                break;
            case '700':
                $error .= 'Unbekannter Fehler';
                break;
            case '801':
                $error .= 'Logodatei nicht angegeben';
                break;
            case '802':
                $error .= 'Logodatei existiert nicht';
                break;
            case '803':
                $error .= 'Klingelton nicht angegeben';
                break;
            case '900':
                $error .= 'Benutzer/Passwort-Kombination falsch';
                break;
            case '902':
                $error .= 'http API für diesen Account deaktiviert';
                break;
            case '903':
                $error .= 'Server IP ist falsch';
                break;
            case '11':
                $error .= 'SMS Carrier temporär nicht verfügbar';
                break;

        }

        $this->throwNotSentException($error);
    }
}
