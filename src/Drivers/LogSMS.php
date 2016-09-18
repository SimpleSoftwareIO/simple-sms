<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use Illuminate\Log\Writer;
use SimpleSoftwareIO\SMS\DoesNotReceive;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class LogSMS implements DriverInterface
{
    use DoesNotReceive;

    /**
     * Laravel Logger.
     *
     * @var \GuzzleHttp\Client
     */
    protected $logger;

    /**
     * Create the CallFire instance.
     *
     * @param Illuminate\Log\Writer $logger
     */
    public function __construct(Writer $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Sends a SMS message.
     *
     * @param \SimpleSoftwareIO\SMS\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message)
    {
        foreach ($message->getTo() as $number) {
            $this->logger->notice("Sending SMS message to: $number");
        }
    }

    /**
     * Creates many IncomingMessage objects and sets all of the properties.
     *
     * @param $rawMessage
     *
     * @return mixed
     */
    protected function processReceive($rawMessage)
    {
        $incomingMessage = $this->createIncomingMessage();
        $incomingMessage->setRaw($rawMessage);
        $incomingMessage->setFrom((string) $rawMessage->FromNumber);
        $incomingMessage->setMessage((string) $rawMessage->TextRecord->Message);
        $incomingMessage->setId((string) $rawMessage['id']);
        $incomingMessage->setTo((string) $rawMessage->ToNumber);

        return $incomingMessage;
    }
}
