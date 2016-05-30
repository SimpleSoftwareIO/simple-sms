<?php
namespace SimpleSoftwareIO\SMS\Drivers;

use infobip\models\SMSRequest;
use infobip\SmsClient;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class InfobipSMS extends AbstractSMS implements DriverInterface
{

    protected $smsRequest;
    /**
     * @var SmsClient
     */
    protected $smsClient;

    /**
     * Constructs the Infobip Instance.
     * @param SmsClient $client
     */
    public function __construct(SmsClient $client)
    {
        $this->smsClient =  $client;
    }

    /**
     * Sends a SMS message.
     *
     * @param OutgoingMessage $message
     * @throws \Exception
     */
    public function send(OutgoingMessage $message)
    {
        $smsMessage = new SMSRequest();
        $smsMessage->senderAddress = $message->getFrom();
        $smsMessage->address = count($message->getTo()) ==1 ? $message->getTo()[0] :  $message->getTo();
        $smsMessage->message = $message->composeMessage();

        return $this->smsClient->sendSMS($smsMessage);
    }


    /**
     * Creates many IncomingMessage objects and sets all of the properties.
     *
     * @throws \RuntimeException
     */
    protected function processReceive($rawMessage)
    {
        throw new \RuntimeException('Infibip does not support Inbound API Calls.');
    }

    /**
     * Checks the server for messages and returns their results.
     *
     * @throws \RuntimeException
     */
    public function checkMessages(Array $options = array())
    {
        throw new \RuntimeException('Infibip does not support Inbound API Calls.');
    }

    /**
     * Gets a single message by it's ID.
     *
     * @throws \RuntimeException
     */
    public function getMessage($messageId)
    {
        throw new \RuntimeException('Infibip does not support Inbound API Calls.');
    }

    /**
     * Receives an incoming message via REST call.
     *
     * @param $raw
     * @return IncomingMessage|void
     * @throws \RuntimeException
     */
    public function receive($raw)
    {
        throw new \RuntimeException('Infibip does not support Inbound API Calls.');
    }

}
