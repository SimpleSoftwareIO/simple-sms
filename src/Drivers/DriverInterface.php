<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use SimpleSoftwareIO\SMS\OutgoingMessage;

interface DriverInterface
{
    /**
     * Sends a SMS message.
     *
     * @param \SimpleSoftwareIO\SMS\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message);

    /**
     * Checks the server for messages and returns their results.
     *
     * @param array $options
     *
     * @return array
     */
    public function checkMessages(array $options = []);

    /**
     * Gets a single message by it's ID.
     *
     * @param string|int $messageId
     *
     * @return \SimpleSoftwareIO\SMS\IncomingMessage
     */
    public function getMessage($messageId);

    /**
     * Receives an incoming message via REST call.
     *
     * @param mixed $raw
     *
     * @return \SimpleSoftwareIO\SMS\IncomingMessage
     */
    public function receive($raw);
}
