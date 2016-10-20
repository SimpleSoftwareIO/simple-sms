<?php

namespace SimpleSoftwareIO\SMS\NotificationChannel;


use SimpleSoftwareIO\SMS\SMS;
use Illuminate\Notifications\Notification;
use SimpleSoftwareIO\SMS\SMSNotSentException;

class SMSChannel
{
    /**
     * @var SMS;
     */
    protected $SMS;


    /**
     * @param SMS $SMS
     */
    public function __construct(SMS $SMS)
    {
        $this->sms = $SMS;
    }

    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     *
     * @throws SMSNotSentException
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('SMS')) {
            return;
        }

        $message = $notification->toSMS($notifiable);
        if (is_string($message)) {
            $message = new SMSMessage($message);
        }

        resolve('sms')->send(trim($message->content), [], function($sms) use ($message, $to) {
            if($message->from)
                $sms->from($message->from);

            $sms->to($to);
        });
    }
}