<?php

use SimpleSoftwareIO\SMS\NotificationChannel\SMSMessage;

class SMSMessageObjectTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function message_object_creates_with_correct_message()
    {
        $test_message_content = 'Lorem ipsum dolor sit amet...';

        $message = new SMSMessage($test_message_content);

        $this->assertEquals($test_message_content, $message->content);
    }

    /** @test */
    public function can_set_object_properties_on_fluently()
    {
        $test_message_content = 'Lorem ipsum dolor sit amet...';
        $test_message_from = '+1123123123';

        $message = SMSMessage::create()
            ->content($test_message_content)
            ->from($test_message_from);

        $this->assertEquals($test_message_content, $message->content);
        $this->assertEquals($test_message_from, $message->from);
    }
}
