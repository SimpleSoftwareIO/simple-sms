<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use SimpleSoftwareIO\SMS\IncomingMessage;
use SimpleSoftwareIO\SMS\SMSNotSentException;

abstract class AbstractSMS
{
    protected $debug;

    /**
     * Throw a not sent exception.
     *
     * @param string   $message
     * @param null|int $code
     *
     * @throws SMSNotSentException
     */
    protected function throwNotSentException($message, $code = null)
    {
        throw new SMSNotSentException($message, $code);
    }

    /**
     * Creates a new IncomingMessage instance.
     *
     * @return IncomingMessage
     */
    protected function createIncomingMessage()
    {
        return new IncomingMessage();
    }

    /**
     * Creates many IncomingMessage objects.
     *
     * @param string $rawMessages
     *
     * @return array
     */
    protected function makeMessages($rawMessages)
    {
        $incomingMessages = [];
        foreach ($rawMessages as $rawMessage) {
            $incomingMessages[] = $this->processReceive($rawMessage);
        }

        return $incomingMessages;
    }

    /**
     * Creates a single IncomingMessage object.
     *
     * @param string $rawMessage
     *
     * @return mixed
     */
    protected function makeMessage($rawMessage)
    {
        return $this->processReceive($rawMessage);
    }

    /**
     * Creates many IncomingMessage objects and sets all of the properties.
     *
     * @param string $rawMessage
     *
     * @return mixed
     */
    abstract protected function processReceive($rawMessage);

    /**
     * Defines if debug is enabled or disabled (SMS77).
     *
     * @param $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }
}
