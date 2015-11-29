Simple SMS
==========

[![Build Status](https://travis-ci.org/SimpleSoftwareIO/simple-sms.svg?branch=master)](https://travis-ci.org/SimpleSoftwareIO/simple-sms)
[![Latest Stable Version](https://poser.pugx.org/simplesoftwareio/simple-sms/v/stable.svg)](https://packagist.org/packages/simplesoftwareio/simple-sms)
[![Latest Unstable Version](https://poser.pugx.org/simplesoftwareio/simple-sms/v/unstable.svg)](https://packagist.org/packages/simplesoftwareio/simple-sms)
[![License](https://poser.pugx.org/simplesoftwareio/simple-sms/license.svg)](https://packagist.org/packages/simplesoftwareio/simple-sms)
[![Total Downloads](https://poser.pugx.org/simplesoftwareio/simple-sms/downloads.svg)](https://packagist.org/packages/simplesoftwareio/simple-sms)

* [Introduction](#docs-introduction)
* [Requirements](#docs-requirements)
* [Configuration](#docs-configuration)
    * [Call Fire Driver](#docs-call-fire-driver)
    * [E-mail Driver](#docs-e-mail-driver)
    * [EZTexting Driver](#docs-ez-texting-driver)
    * [LabsMobile Driver](#docs-labsmobile-driver)
    * [Mozeo Driver](#docs-mozeo-driver)
    * [Nexmo Driver](#docs-nexmo-driver)
    * [Twilio Driver](#docs-twilio-driver)
* [Driver Support](#docs-driver-support)
* [Usage](#docs-usage)
* [Outgoing Message Enclosure](#docs-outgoing-enclosure)
* [Incoming Message](#docs-incoming-message)

<a id="docs-introduction"></a>
## Introduction
Simple SMS is an easy to use package for [Laravel](http://laravel.com/) that adds the capability to send and receive SMS/MMS messages to mobile phones from your web app. It currently supports a free way to send SMS messages through E-Mail gateways provided by the wireless carriers. The package also supports 4 paid services, [Call Fire,](https://www.callfire.com/) [EZTexting,](https://www.eztexting.com) [LabsMobile,](http://www.labsmobile.com) [Mozeo,](https://www.mozeo.com/)[Nexmo,](https://www.nexmo.com/) and [Twilio.](https://www.twilio.com)

<a id="docs-requirements"></a>
## Requirements

#### Laravel 5
* PHP: >= 5.5
* Guzzle >= 6.0

<a id="docs-configuration"></a>
## Configuration

#### Laravel 4

Please read the Laravel 4 [documentation.](https://github.com/SimpleSoftwareIO/simple-sms/blob/master/docs/laravel4.md)

#### Composer

First, add the Simple SMS package to your `require` in your `composer/json` file:

    "require": {
        "simplesoftwareio/simple-sms": "~2"
    }

Next, run the `composer update` command.  This will install the package into your Laravel application.

#### Service Provider

Once you have added the package to your composer file, you will need to register the service provider with Laravel.

Add `SimpleSoftwareIO\SMS\SMSServiceProvider::class` in your `config/app.php` configuration file within the `providers` array.

#### Aliases

Finally, register the Facade.

Add `'SMS' => SimpleSoftwareIO\SMS\Facades\SMS::class` in your `config/app.php` configuration file within the `aliases` array.

#### API Settings

You must run the following command to save your configuration files to your local app:

    php artisan vendor:publish

This will copy the configuration files to your `config` folder.

>Failure to run the `vendor:publish` command will result in your configuration files being overwritten after every `composer update` command.

#### Driver Configuration

<a id="docs-call-fire-driver"></a>
###### Call Fire Driver

This driver sends and receives all messages through the [Call Fire](https://www.callfire.com/) service.  It is a very quick and reliable service provider that includes many features such as drip campaigns and voice services.

Fill in the `config` file with the correct settings to use this driver.  You can find these settings under your CallFire account and then selecting [API Access.](https://www.callfire.com/ui/manage/access)

    return [
        'driver' => 'callfire',
        'from' => 'Not Use For Call Fire',
        'callfire' => [
            'app_login' => 'Your App Login',
            'app_password' => 'Your App Password'
        ],
    ];

>Note: All messages from CallFire come from the same short number (67076)

<a id="docs-e-mail-driver"></a>
###### E-mail Driver

The E-Mail driver sends all messages through the configured e-mail driver for Laravel.  This driver uses the wireless carrier's e-mail gateways to send SMS messages to mobile phones. The biggest benefit to using the e-mail driver is that it is completely free to use.

The only setting for this driver is the `from` setting.  Simply enter an email address that you would like to send messages from.

    return [
        'driver' => 'email',
        'from' => 'example@example.com',
    ];

>If messages are not being sent, ensure that you are able to send E-Mail through Laravel first.

The following are currently supported by using the e-mail gateway driver.

| Country | Carrier | Carrier Prefix | SMS Supported | MMS Supported | Tested? |
| --- | --- | --- | --- | --- | --- |
| USA | AT&T | att | Yes | Yes | Yes |
| USA | Air Fire Mobile | airfiremobile | Yes | No | No |
| USA | Alaska Communicates | alaskacommunicates | Yes | Yes | No |
| USA | Ameritech | ameritech | Yes | No | No |
| USA | Boost Mobile | moostmobile | Yes | Yes | No |
| USA | Clear Talk | cleartalk | Yes | No | No |
| USA | Cricket | cricket | Yes | No | No |
| USA | Metro PCS | metropcs | Yes | Yes | No |
| USA | NexTech | nextech | Yes | No | No |
| Canada | Rogers Wireless | rogerswireless | Yes | Yes | No |
| USA | Unicel | unicel | Yes | Yes | No |
| USA | Verizon Wireless | verizonwireless | Yes | Yes | No |
| USA | Virgin Mobile | virginmobile | Yes | Yes | No |
| USA | T-Mobile | tmobile | Yes | Yes | Yes |

>You must know the wireless provider for the mobile phone to use this driver.

>Careful!  Not all wireless carriers support e-mail gateways around the world.

>Some carriers slightly modify messages by adding the `from` and `to` address to the SMS message.

>An untested gateway means we have not been able to confirm if the gateway works with the mobile provider.  Please provide feedback if you are on one of these carriers.

<a id="docs-ez-texting-driver"></a>
###### EZTexting

This driver sends all messages through the [EZTexting](https://www.eztexting.com) service.  EZTexting has many different options that have proven to be reliable and fast.

Fill in the `config` file with the correct settings to enable EZTexting.

    return [
        'driver' => 'eztexting',
        'from' => 'Not Use For EZTexting',
        'eztexting' => [
            'username' => 'Your Username',
            'password' => 'Your Password'
        ],
    ];

To enable `receive()` for this service, you must visit the [EZTexting settings page.](https://app.eztexting.com/keywords/index/format/apist)  Enable the `Forwarding API` and `Keyword API` for the messages that you would like forwarded to your web application.

>Note: All messages from EZTexting come from the same short number (313131)

<a id="docs-labsmobile-driver"></a>
###### LabsMobile Driver

This driver sends all messages through the [LabsMobile](http://www.labsmobile.com/) service.  These settings can be found on your [API Settings](https://www.labsmobile.com/es/login) page.

    return [
        'driver' => 'labsmobile',
        'from' => 'Sender',
        'labsmobile' => [
            'client' => 'Your Client Key',
            'username' => 'Your Username',
            'password' => 'Your Password',
            'test' => '1 for simulate mode; 0 for real sendings'
        ]
    ];

<a id="docs-mozeo-driver"></a>
###### Mozeo Driver

This driver sends all messages through the [Mozeo](https://www.mozeo.com/) service.  These settings can be found on your [API Settings](https://www.mozeo.com/mozeo/customer/platformdetails.php) page.

    return [
        'driver' => 'mozeo',
        'from' => 'Not Used With Mozeo',
        'mozeo' => [
            'companyKey' => 'Your Company Key',
            'username' => 'Your Username',
            'password' => 'Your Password'
        ]
    ];

>Note: All messages from Mozeo come from the same short number (24587)

<a id="docs-nexmo-driver"></a>
######  Nexmo Driver

This driver sends messages through the [Nexmo](https://www.nexmo.com/product/messaging/) messaging service.  It is very reliable and capable of sending messages to mobile phones worldwide.

    return [
        'driver' => 'nexmo',
        'from' => 'Company Name',
        'nexmo' => [
            'key'       => 'Your Nexmo API Key',
            'secret'    => 'Your Nexmo API Secret'
        ]
    ];

To enable `receive()` messages you must set up the [request URL.](https://docs.nexmo.com/index.php/sms-api/handle-inbound-message)

<a id="docs-twilio-driver"></a>
######  Twilio Driver

This driver sends messages through the [Twilio](https://www.twilio.com/sms) messaging service.  It is very reliable and capable of sending messages to mobile phones worldwide.

    return [
        'driver' => 'twilio',
        'from' => '+15555555555', //Your Twilio Number in E.164 Format.
        'twilio' => [
            'account_sid' => 'Your SID',
            'auth_token' => 'Your Token',
            'verify' => true,  //Used to check if messages are really coming from Twilio.
        ]
    ];

It is strongly recommended to have the `verify` option enabled.  This setting performs an additional security check to ensure messages are coming from Twilio and not being spoofed.

To enable `receive()` messages you must set up the [request URL.](https://www.twilio.com/user/account/phone-numbers/incoming)  Select the number you wish to enable and then enter your request URL.  This request should be a `POST` request.

<a id="docs-driver-support"></a>
##Driver Support

Not all drivers support every method due to the differences in each individual API.  The following table outlines what is supported for each driver.

| Driver | Send | Queue | Pretend | CheckMessages | GetMessage | Receive |
| --- | --- | --- | --- | --- | --- | --- |
| Call Fire | Yes | Yes | Yes | Yes | Yes | No |
| E-Mail | Yes | Yes | Yes | No | No | No |
| EZTexting | Yes | Yes | Yes | Yes | Yes | Yes |
| LabsMobile | Yes | Yes | Yes | No | No | No |
| Mozeo | Yes | Yes | Yes | No | No | No |
| Nexmo | Yes | Yes | Yes | Yes | Yes | Yes |
| Twilio | Yes | Yes | Yes | Yes | Yes | Yes |

<a id="docs-usage"></a>
## Usage

#### Basic Usage

Simple SMS operates in much of the same way as the Laravel Mail service provider.  If you are familiar with this then SMS should feel like home.  The most basic way to send a SMS is to use the following:

    //Service Providers Example
    SMS::send('simple-sms::welcome', $data, function($sms) {
        $sms->to('+15555555555');
    });

    //Email Driver Example
    SMS::send('simple-sms::welcome', $data, function($sms) {
        $sms->to('+15555555555', 'att');
    });

The first parameter is the view file that you would like to use.  The second is the data that you wish to pass to the view.  The final parameter is a callback that will set all of the options on the `message` closure.

#### Send

The `send` method sends the SMS through the configured driver using a Laravel view file.

    SMS::send($view, Array $data, function($sms) {
        $sms->to('+15555555555');
    }
    SMS::send('simple-sms::welcome', $data, function($sms) {
        $sms->to('+15555555555');
    });

It is possible to send a simple message without creating views by passing a string instead of a view.

    SMS::send($message, [], function($sms) {
        $sms->to('+15555555555');
    }
    SMS::send('This is my message', [], function($sms) {
        $sms->to('+15555555555');
    });

#### Driver

The `driver` method will switch the provider during runtime.

    //Will send through default provider set in the config file.
    SMS::queue('simple-sms::welcome', $data, function($sms) {
        $sms->to('+15555555555');
    });

    SMS::driver('twilio');

    //Will send through Twilio
    SMS::queue('simple-sms::welcome', $data, function($sms) {
        $sms->to('+15555555555');
    });

#### Queue

The `queue` method queues a message to be sent later instead of sending the message instantly.  This allows for faster respond times for the consumer by offloading uncustomary processing time. Like `Laravel's Mail` system, queue also has `queueOn,` `later,` and `laterOn` methods.

    SMS::queue('simple-sms::welcome', $data, function($sms) {
        $sms->to('+15555555555');
    });

>The `queue` method will fallback to the `send` method if a queue service is not configured within `Laravel.`

#### Pretend

The `pretend` method will simply create a log file that states that a SMS message has been "sent."  This is useful to test to see if your configuration settings are working correctly without sending actual messages.

    SMS::pretend('simple-sms::welcome', $data, function($sms) {
        $sms->to('+15555555555');
    });

You may also set the `pretend` configuration option to true to have all SMS messages pretend that they were sent.

    `/app/config/simplesoftwareio/simple-sms/config.php`
    return array(
        'pretend' => true,
    );

#### Receive

Simple SMS supports push SMS messages.  You must first configure this with your service provider by following the configuration settings above.

    Route::post('sms/receive', function()
    {
        SMS::receive();
    }

The receive method will return a `IncomingMessage` instance.  You may request any data off of this instance like:

    Route::post('sms/receive', function()
    {
        $incoming = SMS::receive();
        //Get the sender's number.
        $incoming->from();
        //Get the message sent.
        $incoming->message();
        //Get the to unique ID of the message
        $incoming->id();
        //Get the phone number the message was sent to
        $incoming->to();
        //Get the raw message
        $incoming->raw();
    }

The `raw` method returns all of the data that a driver supports.  This can be useful to get information that only certain service providers provide.

    Route::post('sms/receive', function()
    {
        $incoming = SMS::receive();
        //Twilio message status
        echo $incoming->raw()['status'];
    }

The above would return the status of the message on the Twilio driver.

>Data used from the `raw` method will not work on other service providers.  Each provider has different values that are sent out with each request.

#### Check Messages

This method will retrieve an array of messages from the service provider.  Each message within the array will be an `IncomingMessage` object.

    $messages = SMS::checkMessages();
    foreach ($messages as $message)
    {
        //Will display the message of each retrieve message.
        echo $message->message();
    }

The `checkMessages` method supports has an `options` variable to pass some settings onto each service provider. See each service providers API to see which `options` may be passed.

More information about each service provider can be found at their API docs.

* [Call Fire](https://www.callfire.com/api-documentation/rest/version/1.1#!/text/QueryTexts_get_1)
* [EZTexting](https://www.eztexting.com/developers/sms-api-documentation/rest)
* [LabsMobile](http://www.labsmobile.com/en/api-sms)
* [Mozeo](https://www.mozeo.com/mozeo/customer/Mozeo_API_OutboundSMS.pdf)
* [Nexmo](https://docs.nexmo.com/index.php/developer-api/search-message)
* [Twilio](https://www.twilio.com/docs/api/rest/message#list-get)

#### Get Message

You are able to retrieve a message by it's ID with a simply call.  This will return an IncomingMessage object.

    $message = SMS::getMessage('aMessageId');
    //Prints who the message came from.
    echo $message->from();

<a id="docs-outgoing-enclosure"></a>
## Outgoing Message Enclosure

#### Why Enclosures?

We use enclosures to allow for functions such as the queue methods.  Being able to easily save the message enclosures allows for much greater flexibility.

#### To

The `to` method adds a phone number that will have a message sent to it.

    //Service Providers Example
    SMS::send('simple-sms::welcome', $data, function($sms) {
        $sms->to('+15555555555');
        $sms->to('+14444444444');
    });
    //Email Driver
    SMS::send('simple-sms::welcome', $data, function($sms) {
        $sms->to('15555555555', 'att);
        $sms->to('14444444444', 'verizonwireless);
    });

>The carrier is required for the email driver so that the correct email gateway can be used.  See the table above for a list of accepted carriers.

#### From

The `from` method will set the address from which the message is being sent.

    SMS::send('simple-sms::welcome', $data, function($sms) {
        $sms->from('+15555555555');
    });

#### attachImage

The `attachImage` method will add an image to the message.  This will also convert the message to a MMS because SMS does not support image attachments.

    //Email Driver
    SMS::send('simple-sms::welcome', $data, function($sms) {
        $sms->attachImage('/local/path/to/image.jpg');
    });
    //Twilio Driver
    SMS::send('simple-sms::welcome', $data, function($sms) {
        $sms->attachImage('/url/to/image.jpg');
    });

>Currently only supported with the E-Mail and Twilio Driver.

<a id="docs-incoming-message"></a>
## Incoming Message

All incoming messages generate a `IncomingMessage` object.  This makes it easy to retrieve information from them in a uniformed way across multiple service providers.

#### Raw

The `raw` method returns the raw data provided by a service provider.

    $incoming = SMS::getMessage('messageId');
    echo $incoming->raw()['status'];

>Each service provider has different information in which they supply in their requests.  See their documentations API for information on what you can get from a `raw` request.

#### From

This method returns the phone number in which a message came from.

    $incoming = SMS::getMessage('messageId');
    echo $incoming->from();

#### To

The `to` method returns the phone number that a message was sent to.

    $incoming = SMS::getMessage('messageId');
    echo $incoming->to();

#### Id

This method returns the unique id of a message.

    $incoming = SMS::getMessage('messageId');
    echo $incoming->id();

#### Message

And the best for last; this method returns the actual message of a SMS.

    $incoming = SMS::getMessage('messageId');
    echo $incoming->message();
