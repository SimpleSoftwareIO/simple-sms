[![Build Status](https://travis-ci.org/SimpleSoftwareIO/simple-sms.svg?branch=master)](https://travis-ci.org/SimpleSoftwareIO/simple-sms)
[![Latest Stable Version](https://poser.pugx.org/simplesoftwareio/simple-sms/v/stable.svg)](https://packagist.org/packages/simplesoftwareio/simple-sms)
[![Latest Unstable Version](https://poser.pugx.org/simplesoftwareio/simple-sms/v/unstable.svg)](https://packagist.org/packages/simplesoftwareio/simple-sms)
[![License](https://poser.pugx.org/simplesoftwareio/simple-sms/license.svg)](https://packagist.org/packages/simplesoftwareio/simple-sms)
[![Total Downloads](https://poser.pugx.org/simplesoftwareio/simple-sms/downloads.svg)](https://packagist.org/packages/simplesoftwareio/simple-sms)

Simple SMS
==========

- [Introduction](#introduction)
- [Configuration](#configuration)
    -[Email Driver](#emailDriver)
    -[Twilio Driver](#twilioDriver)
- [Simple Ideas](#simple-ideas)
- [Usage](#usage)
- [Outgoing Message Enclsoure](#message-enclosure)
- [Incoming Message](#incoming-message)

##This non-stable release of Simple-SMS is currently under development.  Expect bugs.  The API is currently unstable and is anticipated to change.

<a id="introduction"></a>
## Introduction

Simple SMS is an easy to use package for [Laravel](http://laravel.com/) that adds the capablility to send SMS/MMS messages to mobile phones. It currently supports a free way to accomplish this, by using E-Mail gateways, as well as paid methods, through service providers such as [Twililo](http://www.twilio.com/sms).

<a id="configuration"></a>
## Configuration

#### Composer

First, add the Simple SMS package to your `require` in your `composer/json` file:

	"require": {
		"simplesoftwareio/simple-sms": "*"
	}

Next, run the `composer update` command.  This will install the package into your Laravel application.

>Heads up! Your `minimum-stability` will need to be set to `dev` in your root `composer.json` file because this software is not yet considered stable.

#### Service Provider

Once you have added the package to your composer file, you will need to register the service provider with Laravel.  This is done by adding `'SimpleSoftwareIO\SMS\SMSServiceProvider'` in your `app/config/app.php` configuration file within the `providers` array.

#### Aliases

Finally, register the `'SMS' => 'SimpleSoftwareIO\SMS\Facades\SMS'` in your `app/config/app.php` configuration file within the `aliases` array.

#### API Settings

You must run the following command to save your configuration files to your local app:

	php artisan config:publish simplesoftwareio/simple-sms

This will copy the configuration files to your `app/config/simplesoftwareio/simple-sms` folder.

>Failure to run the `config:publish` command will result in your configuration files being overwritten after every `composer update` command.

<a id="emailDriver"></a>
###### E-mail Driver

The e-mail driver sends all messages through the configured e-mail driver for Laravel.  This driver uses the wireless carrier's e-mail gateways to send SMS messages to mobile phones.

The only setting for this driver is the `from` setting.  Simply enter an email address that you would like to send messages from.

	return [
		'driver' => 'email',
		'from' => 'example@example.com',
	];

>If messages are not being sent, ensure that you are able to send E-Mail through Laravel first.

The biggest benefit to using the e-mail driver is that it is completely free to use.

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
| USA | T-Mobile | tmobile | Yes | Yes | No |

>We will be adding more carriers from around the world after we find testers.

>Careful!  Not all wireless carriors support e-mail gateways around the world.

>Some carriers slightly modifiy messages by adding the `from` and `to` address to the SMS message.

>An untested gateway means we have not been able to confirm if the gateway works with the mobile provider.  Please provide feedback if you are on one of these carriers.

<a id="twilioDriver"></a>
######  Twilio Driver

This driver sends messages through the [Twilio](https://www.twilio.com/sms) messaging service.  It is very reliable and capable of sending messages to mobile phones worldwide.  Simply supply your Account SID and Auth Token to begin sending messages.

	return [
		'driver' => 'twilio',
		'from' => '+15555555555',
		'twilio' => [
			'account_sid' => 'Your SID',
			'auth_token' => 'Your Token',
			'verify' => true,
		]
	];

>The Twilio driver cost money to use.

**`from`**

The `from` setting must be a number that you own on Twilio and be in `E.164` format.

**`account_sid`**

Supply your `account_sid` found at [Twilio.](https://www.twilio.com/user/account)

**`auth_token`**

Supply your `auth_token` found at [Twilio.](https://www.twilio.com/user/account)

**`verify**

This setting can be `true` or `false.`  When this setting is enabled, it will validate all requests for a SMS push message.  It is recommended to have this enabled for security reasons.

**Push Messages**

The Twilio driver supports pushed messages to your web application.  First, you must set up the [request URL.](https://www.twilio.com/user/account/phone-numbers/incoming)  Select the number you wish to enable and then enter your request URL.  This request should be a `POST` request.

<a id="simple-ideas"></a>
## Simple Ideas

Coming Soon.

<a id="usage"></a>
## Usage

#### Basic Usage

Simple SMS operates in much of the same way as the Laravel Mail service provider.  If you are familiar with this then SMS should feel like home.  The most basic way to send a SMS is to use the following:

	//Twilio Example
	SMS::send('simple-sms::welcome', $data, function() {
		$sms->to('+15555555555');
	});
  
	//Email Example
	SMS::send('simple-sms::welcome', $data, function() {
		$sms->to('+15555555555', 'att');
	});

The first paramenter is the view file that you would like to use.  The second is the data that you wish to pass to the view.  The final parameter is a callback that will set all of the options on the `message` closure.

#### Send

The `send` method sends the SMS through the configured driver.

	SMS::send('simple-sms::welcome', $data, function() {
		$sms->to('+15555555555');
	});

#### Queue

###### Coming in Alpha2

The `queue` method queues a message to be sent later instead of sending the message instantly.  This allows for faster respond times for the consumer by offloading unessessary processing to a later time.

	SMS::queue('simple-sms::welcome', $data, function() {
		$sms->to('+15555555555');
	});

>The `queue` method will fallback to the `send` method if a queue service is not configured within `Laravel.`

#### Pretend

The `pretend` method will simply create a log file that states that a SMS message has been "sent."  This is useful to test to see if your configuration settings are working correctly without sending actual messages.

	SMS::pretend('simple-sms::welcome', $data, function() {
		$sms->to('+15555555555');
	});

You may also set the `pretend` configuration option to true to have all SMS messages pretend that they were sent.

	`/app/config/simplesoftwareio/simple-sms/config.php`
	return array(
		'pretend' => true,
	);

#### Receive

Simple SMS supports push SMS messages.

    Route::post('sms/receive', function()
    {
        SMS::receive();
    }

The receive method will return a IncomingMessage instance.  You may request any data off of this instance like:

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

The above would return the status of the message.

>Data used from the `raw` method will not work on other service providers.  Each provider has different values that are sent out with each request.

#### Check Messages

This method will retrieve an array of messages from the service provider.  Each message return will be an IncomingMessage object.

    $messages = SMS::checkMessages();
    foreach ($messages as $message)
    {
        //Will display the message of each retrieve message
        echo $message->message();
    }

The `checkMessages` method supports has an `options` variable to pass some settings onto the service provider.  All drivers have a `start` and `end` option to determine where to start and end retrieving of messages.

    //Will get the first 25 messages at the service provider.
    $messages = SMS::checkMessages(['start' => 0, 'end' => 25]);

Some service providers also support filtering of messages.

    //Twilio example to filter from numbers and date.
    $messages = SMS::checkMessages(['From'] => '+15555555555', 'DateSent' => '2009-07-06');

More information about each service provider can be found at their API docs.
*[Twilio](https://www.twilio.com/docs/api/rest/message#list-get)

#### Get Message

You are able to retrieve a message by it's ID with a simply call.  This will return an IncomingMessage object.

    SMS::getMessage('aMessageId');

<a id="enclosures"></a>
## Outgoing Message Enclosure

#### Why Enclosures?

We use enclosures to allow for functions such as the queue methods.  Being able to easily save the message enclousure allows for a much greater flexibilty in the longer term, in return for a slightly more difficult to use package.

#### To

The `to` method adds a phone number to the sending array.  Any phone number in this array will have a message sent to it. It accepts an array of numbers, or a single number as a paramenter.

	//Twilio Driver
	SMS::send('simple-sms::welcome', $data, function() {
		$sms->to('+15555555555');
		$sms->to('+14444444444');
	});
	//Email Driver
	SMS::send('simple-sms::welcome', $data, function() {
		$sms->to('+15555555555', 'att);
		$sms->to('+14444444444', 'verizonwireless);
	});

>The carrier is required for the email driver so that the correct email gateway can be used.  See the table above for a list of accepted carriers.

#### From

The `from` method will set the address from which the message is being sent.

	SMS::send('simple-sms::welcome', $data, function() {
		$sms->from('+15555555555');
	});

#### attachImage

The `attachImage` method will add an image to the message.  This will also convert the message to a MMS because SMS does not support image attachments.

	SMS::send('simple-sms::welcome', $data, function() {
		$sms->attachImage('/path/to/image.jpg');
	});

>Twilio does not currently support attached images.

<a id="incoming-message"></a>
## Incoming Message

All incoming messages generate a Incoming Message object.  This makes it easy to retrieve information from them in a uniformed way across multiple service providers.

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

And the best for last;  this method returns the actual message of a SMS.

        $incoming = SMS::getMessage('messageId');
        echo $incoming->message();