<?php namespace SimpleSoftwareIO\SMS\Drivers;

/**
 * Simple-SMS
 * A simple SMS message sendingn for Laravel.
 *
 * @link http://www.simplesoftware.io
 * @author SimpleSoftware support@simplesoftware.io
 *
 */

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