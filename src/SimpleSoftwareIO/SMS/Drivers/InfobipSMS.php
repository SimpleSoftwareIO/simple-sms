<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 4/3/16
 * Time: 1:01 PM
 */

namespace SimpleSoftwareIO\SMS\Drivers;

use infobip\models\SMSRequest;
use infobip\SmsClient;
use SimpleSoftwareIO\SMS\OutgoingMessage;

/**
 * Class InfobipSMS
 * @package SimpleSoftwareIO\SMS\Drivers
 */
class InfobipSMS extends AbstractSMS implements DriverInterface
{

    protected $smsRequest;
    protected $smsClient;

    /**
     * @param SmsClient $client
     */
    public function __construct(SmsClient $client)
    {
        $this->smsClient =  $client;
    }


    protected function processReceive($rawMessage)
    {
        // TODO: Implement processReceive() method.
    }

    public function send(OutgoingMessage $message)
    {
        // TODO: Implement send() method.

        $smsMessage = new SMSRequest();
        $smsMessage->senderAddress = $message->getFrom();
        $smsMessage->address = count($message->getTo()) ==1 ? $message->getTo()[0] :  $message->getTo();
        $smsMessage->message = $message->composeMessage();
        $smsMessageSendResult = $this->smsClient->sendSMS($smsMessage);
        $smsMessageStatus = $this->smsClient->queryDeliveryStatus($smsMessageSendResult);

        if( ! $smsMessageStatus->isSuccess()) {
            $exceptionMsg =  'Message id:'. $smsMessageStatus->exception->messageId .
                'Text:' . $smsMessageStatus->exception->text.
                'Variables:' .$smsMessageStatus->exception->variables;
            throw new \Exception($exceptionMsg);
        }

    }

    public function checkMessages(array $options = [])
    {
        // TODO: Implement checkMessages() method.
    }

    public function getMessage($messageId)
    {
        // TODO: Implement getMessage() method.
    }

    public function receive($raw)
    {
        // TODO: Implement receive() method.
    }

}