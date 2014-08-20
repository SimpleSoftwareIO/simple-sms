<?php namespace SimpleSoftwareIO\SMS\Drivers;

use SimpleSoftwareIO\SMS\Message;
use Illuminate\Mail\Mailer;

class EmailSMS implements DriverInterface {

  /**
   * The Message Instance
   *
   * @var SimpleSoftwareIO\SMS\Message
   */
  protected $message;

  /**
   * Creates the EmailSMS Instance.
   *
   * @parma Illuminate\Mail\Mailer $mailer Illuminate Mailer
   * @return void
   */
  public function __construct(Mailer $mailer)
  {
    $this->mailer = $mailer;
  }

  /**
   * Sends a SMS message
   *
   * @parma SimpleSoftwareIO\SMS\Message @messasge The message class.
   * @return void
   */
  public function send(Message $message)
  {
    $this->message = $message;
    $me = $this;

    $this->mailer->send($this->message->getView(), $this->message->getData(), function($email) use ($me)
                        {
                          foreach($me->message->getTo() as $number)
                          {
                            $email->to($me->buildEmail($number));
                          }

                          if ($me->message->getAttachImages())
                          {
                            foreach($me->message->getAttachImages() as $image)
                            {
                              $email->attach($image);
                            }
                          }

                          $email->from($me->message->getFrom());
                        });
  }

  /**
   * Builds the email address of a number.
   *
   * @parma array $number The number and carrier to look up.
   * @return string
   */
  protected function buildEmail($number)
  {
    return $number['number'] . '@' . $this->lookupGateway($number['carrier'], $this->message->isMMS());
  }

  /**
   * Finds the gateway based on the carrier and MMS.
   * 
   * @parm string $carrier The desired carrier to look up.
   * @parma boolean $mms If the Message is an MMS.
   * @return string
   */
  protected function lookupGateway($carrier, $mms)
  {
    if($mms)
    {
      switch($carrier)
      {
        case 'att':
        return 'mms.att.net';

        case 'airfiremobile':
        throw new InvalidArgumentException('Air Fire Mobile does not support Email Gateway MMS messages.');

        case 'alaskacommunicates':
        return 'msg.acsalaska.com';

        case 'ameritech':
        throw new InvalidArgumentException('Ameritech does not support Email Gateway MMS messages.');

        case 'assurancewireless':
        return 'vmobl.com';

        case 'boostmobile':
        return 'myboostmobile.com';

        case 'cleartalk':
        throw new InvalidArgumentException('Clear Talk does not support Email Gateway MMS messages.');

        case 'cricket':
        return 'mms.mycricket.com ';

        case 'metropcs':
        return 'mymetropcs.com';

        case 'nextech':
        throw new InvalidArgumentException('NexTech does not support Email Gateway MMS messages.');

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
      }
    }
    else
    {
      switch($carrier)
      {
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
      }
    }
  }
}