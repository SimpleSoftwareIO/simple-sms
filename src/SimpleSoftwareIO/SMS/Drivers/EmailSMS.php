<?php

namespace SimpleSoftwareIO\SMS\Drivers;

use Illuminate\Mail\Mailer;
use InvalidArgumentException;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class EmailSMS implements DriverInterface
{
    /**
     * Creates the EmailSMS Instance.
     *
     * @param \Illuminate\Mail\Mailer $mailer Illuminate Mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Sends a SMS message.
     *
     * @param \SimpleSoftwareIO\SMS\OutgoingMessage $thisssage
     */
    public function send(OutgoingMessage $message)
    {
        $this->outgoingMessage = $message;

        try {
          $this->mailer->send(['text' => $this->outgoingMessage->getView()], $this->outgoingMessage->getData(), function ($email) {
              foreach ($this->outgoingMessage->getToWithCarriers() as $number) {
                  $email->to($this->buildEmail($number));
              }

              if ($this->outgoingMessage->getAttachImages()) {
                  foreach ($this->outgoingMessage->getAttachImages() as $image) {
                      $email->attach($image);
                  }
              }

              $email->from($this->outgoingMessage->getFrom());
          });
        } catch (InvalidArgumentException $e) {
          $this->sendRaw($message);
        }

    }

    protected function sendRaw()
    {
      $this->mailer->raw($this->outgoingMessage->getView(), function ($email) {
          foreach ($this->outgoingMessage->getToWithCarriers() as $number) {
              $email->to($this->buildEmail($number));
          }

          if ($this->outgoingMessage->getAttachImages()) {
              foreach ($this->outgoingMessage->getAttachImages() as $image) {
                  $email->attach($image);
              }
          }

          $email->from($this->outgoingMessage->getFrom());
      });
    }

    /**
     * Builds the email address of a number.
     *
     * @param array $number The number and carrier to look up.
     *
     * @return string
     */
    protected function buildEmail($number)
    {
        if (!$number['carrier']) {
            throw new \InvalidArgumentException('A carrier must be specified if using the E-Mail Driver.');
        }

        return $number['number'].'@'.$this->lookupGateway($number['carrier'], $this->outgoingMessage->isMMS());
    }

    /**
     * Finds the gateway based on the carrier and MMS.
     *
     * @param string $carrier The desired carrier to look up.
     * @param bool   $mms     If the Message is an MMS.
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

                default:
                    throw new \InvalidArgumentException('Carrier specified is not found.');
            }
        }
    }

    /**
     * Checks the server for messages and returns their results.
     *
     * @param array $options
     *
     * @return array
     */
    public function checkMessages(array $options = [])
    {
        throw new \RuntimeException('Receive methods are not support with the E-Mail driver.');
    }

    /**
     * Gets a single message by it's ID.
     *
     * @param string|int $thisssageId
     *
     * @return \SimpleSoftwareIO\SMS\IncomingMessage
     *
     * @throws \RuntimeException
     */
    public function getMessage($thisssageId)
    {
        throw new \RuntimeException('Receive methods are not support with the E-Mail driver.');
    }

    /**
     * Receives an incoming message via REST call.
     *
     * @param mixed $raw
     *
     * @return \SimpleSoftwareIO\SMS\IncomingMessage
     *
     * @throws \RuntimeException
     */
    public function receive($raw)
    {
        throw new \RuntimeException('Receive methods are not support with the E-Mail driver.');
    }
}
