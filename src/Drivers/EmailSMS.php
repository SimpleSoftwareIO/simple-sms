<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use Illuminate\Mail\Mailer;
use InvalidArgumentException;
use SimpleSoftwareIO\SMS\DoesNotReceive;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class EmailSMS implements DriverInterface
{
    use DoesNotReceive;

    /**
     * Creates the EmailSMS Instance.
     *
     * @param \Illuminate\Mail\Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Sends a SMS message via the mailer.
     *
     * @param SimpleSoftwareIO\SMS\OutgoingMessage $message
     *
     * @return Illuminate\Mail\Message
     */
    public function send(OutgoingMessage $message)
    {
        try {
            $message = $this->mailer->send(['text' => $message->getView()], $message->getData(), function ($email) use ($message) {
                $this->generateMessage($email, $message);
            });
        } catch (InvalidArgumentException $e) {
            $message = $this->sendRaw($message);
        }

        return $message;
    }

    /**
     * Generates the Laravel Message Object.
     *
     * @param Illuminate\Mail\Message              $email
     * @param SimpleSoftwareIO\SMS\OutgoingMessage $message
     *
     * @return Illuminate\Mail\Message
     */
    protected function generateMessage($email, $message)
    {
        foreach ($message->getToWithCarriers() as $number) {
            $email->to($this->buildEmail($number, $message));
        }

        if ($message->getAttachImages()) {
            foreach ($message->getAttachImages() as $image) {
                $email->attach($image);
            }
        }

        $email->from($message->getFrom());

        return $email;
    }

    /**
     * Sends a SMS message via the mailer using the raw method.
     *
     * @param SimpleSoftwareIO\SMS\OutgoingMessage $message
     *
     * @return Illuminate\Mail\Message
     */
    protected function sendRaw(OutgoingMessage $message)
    {
        $message = $this->mailer->raw($message->getView(), function ($email) use ($message) {
            $this->generateMessage($email, $message);
        });

        return $message;
    }

    /**
     * Builds the email address of a number.
     *
     * @param array                                $number
     * @param SimpleSoftwareIO\SMS\OutgoingMessage $message
     *
     * @return string
     */
    protected function buildEmail($number, OutgoingMessage $message)
    {
        if (! $number['carrier']) {
            throw new \InvalidArgumentException('A carrier must be specified if using the E-Mail Driver.');
        }

        return $number['number'].'@'.$this->lookupGateway($number['carrier'], $message->isMMS());
    }

    /**
     * Finds the gateway based on the carrier and MMS.
     *
     * @param string $carrier
     * @param bool   $mms
     *
     * @return string
     */
    protected function lookupGateway($carrier, $mms)
    {
        if ($mms) {
            switch ($carrier) {
                case 'att':
                    return 'mms.att.net';

                case 'airfiremobile':
                    throw new \InvalidArgumentException('Air Fire Mobile does not support Email Gateway MMS messages.');
                case 'alaskacommunicates':
                    return 'msg.acsalaska.com';

                case 'ameritech':
                    throw new \InvalidArgumentException('Ameritech does not support Email Gateway MMS messages.');
                case 'assurancewireless':
                    return 'vmobl.com';

                case 'boostmobile':
                    return 'myboostmobile.com';

                case 'cleartalk':
                    throw new \InvalidArgumentException('Clear Talk does not support Email Gateway MMS messages.');
                case 'cricket':
                    return 'mms.mycricket.com ';

                case 'metropcs':
                    return 'mymetropcs.com';

                case 'nextech':
                    throw new \InvalidArgumentException('NexTech does not support Email Gateway MMS messages.');
                case 'projectfi':
                    return 'msg.fi.google.com';

                case 'rogerswireless':
                    return 'mms.rogers.com';

                case 'unicel':
                    return 'utext.com';

                case 'verizonwireless':
                    return 'vzwpix.com';

                case 'virginmobile':
                    return 'vmpix.com';

                case 'tmobile':
                    return 'tmomail.net';

                case 'sprint':
                    return 'pm.sprint.com';

                case 'uscellular':
                    return 'mms.uscc.net';

                default:
                    throw new \InvalidArgumentException('Carrier specified is not found.');
            }
        } else {
            switch ($carrier) {
                case 'att':
                    return 'txt.att.net';

                case 'airfiremobile':
                    return 'sms.airfiremobile.com';

                case 'alaskacommunicates':
                    return 'msg.acsalaska.com';

                case 'ameritech':
                    return 'paging.acswireless.com';

                case 'assurancewireless':
                    return 'vmobl.com';

                case 'boostmobile':
                    return 'sms.myboostmobile.com';

                case 'cleartalk':
                    return 'sms.cleartalk.us';

                case 'cricket':
                    return 'sms.mycricket.com';

                case 'metropcs':
                    return 'mymetropcs.com';

                case 'nextech':
                    return 'sms.ntwls.net';

                case 'projectfi':
                    return 'msg.fi.google.com';

                case 'rogerswireless':
                    return 'sms.rogers.com';

                case 'unicel':
                    return 'utext.com';

                case 'verizonwireless':
                    return 'vtext.com';

                case 'virginmobile':
                    return 'vmobl.com';

                case 'tmobile':
                    return 'tmomail.net';

                case 'sprint':
                    return 'messaging.sprintpcs.com';

                case 'uscellular':
                    return 'email.uscc.net';

                default:
                    throw new \InvalidArgumentException('Carrier specified is not found.');
            }
        }
    }
}
