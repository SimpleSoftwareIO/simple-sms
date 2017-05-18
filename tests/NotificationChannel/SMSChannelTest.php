<?php

use Mockery as m;
use SimpleSoftwareIO\SMS\NotificationChannel\SMSChannel;
use SimpleSoftwareIO\SMS\NotificationChannel\SMSMessage;
use SimpleSoftwareIO\SMS\OutgoingMessage;
use SimpleSoftwareIO\SMS\SMS;

class SMSChannelTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if(!class_exists('\Illuminate\Notifications\Notification'))
            $this->markTestSkipped('Testing of SMSChannel skipped because illuminate/notifications isn\'t installed. Run "composer require illuminate/notifications" and try again.');
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @param \SimpleSoftwareIO\SMS\Drivers\DriverInterface $driver
     * @return SMS
     */
    private function prepare_sms_object_for_test($driver)
    {
        $view_factory = m::mock(\Illuminate\View\Factory::class, function ($factory) {
            $factory->shouldReceive('make')->andThrow(new \InvalidArgumentException);
        });

        $container = m::mock(\Illuminate\Container\Container::class, function ($container) use ($view_factory) {
            $container->shouldReceive('offsetGet')->with('view')->andReturn($view_factory);
        });

        $SMS = new SMS($driver);
        $SMS->setContainer($container);
        $SMS->alwaysFrom('4444444444');
        return $SMS;
    }

    public function testSmsIsSentViaSMS()
    {
        $notification = new NotificationSMSChannelTestNotification;
        $notifiable = new NotificationSMSChannelTestNotifiable;
        $driver = new NotificationSMSDriverTestCustomFromNotification;
        $SMS = $this->prepare_sms_object_for_test($driver);
        $channel = new SMSChannel($SMS);

        $channel->send($notifiable, $notification);

        $this->assertCount(1, $driver->sent_messages);
        $this->assertEquals($driver->sent_messages[0]->composeMessage(), 'this is my message');
        $this->assertEquals($driver->sent_messages[0]->getFrom(), '4444444444');
        $this->assertCount(1, $driver->sent_messages[0]->getTo());
        $this->assertEquals($driver->sent_messages[0]->getTo()[0], '5555555555');
    }

    public function testSmsIsSentViaSMSWithCustomFrom()
    {
        $notification = new NotificationSMSChannelTestCustomFromNotification;
        $notifiable = new NotificationSMSChannelTestNotifiable;
        $driver = new NotificationSMSDriverTestCustomFromNotification;
        $SMS = $this->prepare_sms_object_for_test($driver);
        $channel = new SMSChannel($SMS);

        $channel->send($notifiable, $notification);

        $this->assertCount(1, $driver->sent_messages);
        $this->assertEquals($driver->sent_messages[0]->composeMessage(), 'this is my message with custom from number');
        $this->assertEquals($driver->sent_messages[0]->getFrom(), '5554443333');
        $this->assertCount(1, $driver->sent_messages[0]->getTo());
        $this->assertEquals($driver->sent_messages[0]->getTo()[0], '5555555555');
    }
}
/* Prepare test objects */
if(class_exists('\Illuminate\Notifications\Notification')){
    class NotificationSMSChannelTestNotifiable
    {
        use \Illuminate\Notifications\Notifiable;
        public $phone_number = '5555555555';

        public function routeNotificationForSMS()
        {
            return $this->phone_number;
        }
    }

    class NotificationSMSChannelTestNotification extends \Illuminate\Notifications\Notification
    {
        public function toSMS($notifiable)
        {
            return new SMSMessage('this is my message');
        }
    }

    class NotificationSMSChannelTestCustomFromNotification extends \Illuminate\Notifications\Notification
    {
        public function toSMS($notifiable)
        {
            return SMSMessage::create('this is my message with custom from number')
                ->from('5554443333');
        }
    }

    class NotificationSMSDriverTestCustomFromNotification implements \SimpleSoftwareIO\SMS\Drivers\DriverInterface {
        use \SimpleSoftwareIO\SMS\DoesNotReceive;

        /**
         * @var OutgoingMessage[]
         */
        public $sent_messages = [];

        public function send(OutgoingMessage $message)
        {
            $this->sent_messages[] = $message;
        }
    }
}