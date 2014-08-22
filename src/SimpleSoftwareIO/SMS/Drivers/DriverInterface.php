<?php namespace SimpleSoftwareIO\SMS\Drivers;

use SimpleSoftwareIO\SMS\Message;

interface DriverInterface
{
    /**
     * Sends a SMS message
     *
     * @parma SimpleSoftwareIO\SMS\Message @messasge The message class.
     * @return void
     */
    public function send(Message $message);
}