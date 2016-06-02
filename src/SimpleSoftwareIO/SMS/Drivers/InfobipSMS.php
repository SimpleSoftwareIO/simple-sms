<?php
namespace SimpleSoftwareIO\SMS\Drivers;

use infobip\SmsClient;
use infobip\models\SMSRequest;
use SimpleSoftwareIO\SMS\DoesNotReceive;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class InfobipSMS extends AbstractSMS implements DriverInterface
{
    use DoesNotReceive;

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
}
