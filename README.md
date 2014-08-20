[![Build Status](https://travis-ci.org/SimpleSoftwareIO/simple-sms.svg?branch=master)](https://travis-ci.org/SimpleSoftwareIO/simple-sms)
[![Latest Stable Version](https://poser.pugx.org/simplesoftwareio/simple-sms/v/stable.svg)](https://packagist.org/packages/simplesoftwareio/simple-sms)
[![Latest Unstable Version](https://poser.pugx.org/simplesoftwareio/simple-sms/v/unstable.svg)](https://packagist.org/packages/simplesoftwareio/simple-sms)
[![License](https://poser.pugx.org/simplesoftwareio/simple-sms/license.svg)](https://packagist.org/packages/simplesoftwareio/simple-sms)
[![Total Downloads](https://poser.pugx.org/simplesoftwareio/simple-sms/downloads.svg)](https://packagist.org/packages/simplesoftwareio/simple-sms)

Simple SMS
==========

- [Introduction](#introduction)
- [Configuration](#configuration)
- [Simple Ideas](#simple-ideas)
- [Usage](#usage)
- [Message Enclsoure](#message-enclosure)

<a id="introduction"></a>
## Introduction

Simple SMS is an easy to use package for [Laravel](http://laravel.com/) that is able to send SMS/MMS messages to mobile phones. It currently supports a free way to accomplish this using E-Mail gateways as well as paid methods through service providers such as [Twililo](http://www.twilio.com/sms/pricing).

<a id="configuration"></a>
## Configuration

#### Composer

First, add the Simple SMS package to your `require` in your `composer/json` file:

	"require": {
		"simplesoftwareio/simple-sms": "*"
	}

Next, run the `composer update` command.  This will install the package into your Laravel application.

>Your `minimum-stability` will need to be set to `dev` in your root `composer.json` file because this software is not yet considered stable.

#### Service Provider

Once you have added the package to your composer file; you will need to register the service provider with Laravel.  This is done by adding `'SimpleSoftwareIO\SMS\SMSServiceProvider'` in your `app/config/app.php` configuration file within the `providers` array.

#### Aliases

Finally, register the `'SMS' => 'SimpleSoftwareIO\SMS\Facades\SMS'` in your `app/config/app.php` configuration file within the `aliases` array.

#### API Settings

You must run the following `php artisan config:publish simplesoftwareio/simple-sms` command to save your configuration files to your local app.  This will copy the configuration files to your `app/config/simplesoftwareio/simple-sms` folder.

>Failure to run the `config:publish` command will result in your configuration files being overwritten after every `composer update` command.

###### E-mail Driver

The e-mail driver sends all messages through the configured e-mail driver for Laravel.  This driver uses the wireless carriers e-mail gateways to send SMS messages to mobile phones.

The only setting for this driver is the `from` setting.  Simply enter an email address that you would like to send messages from.

	return [
		'driver' => 'email',
		'from' => 'example@example.com',
	];

>If messages are not being sent; ensure that you are able to send E-Mail through Laravel first.

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

>We will be adding more carriers from around the world once we find testers.

>Careful!  Not all wireless carriors support e-mail gateways around the world.

>Some carriers slightly modifiy messages by adding the `from` and `to` address to the SMS message.

>An untested gateway means we have not been able to confirm if the gateway works with the mobile provider.  Please provide feedback if you are on one of these carriers.

###### Twilio Driver

This driver sends messages through the [Twilio](https://www.twilio.com/sms) messaging service.  It is very reliable and is capable of sending messages to mobile phones worldwide.  Simply supply your Account SID and Auth Token to begin sending messages.

	return [
		'driver' => 'twilio',
		'from' => '+15555555555',
		'twilio' => [
			'account_sid' => 'Your SID',
			'auth_token' => 'Your Token'
		]
	];

>The Twilio driver cost money to use.

<a id="ideas"></a>
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

The first paramenter is the view file that you would like to use.  The second is the data that you wish to pass to the view.  The finally parameter is a callback that will sets all of the options on the `message` closure.

#### Send

The `send` method sends the SMS through the configured driver.

	SMS::send('simple-sms::welcome', $data, function() {
		$sms->to('+15555555555');
	});

#### Queue

###### Coming in Alpha2

The `queue` method queues a message to be sent instead of sending the message instantly.  This allows for faster respond times for the consumer by offloading unessessary processing to a later time.

	SMS::queue('simple-sms::welcome', $data, function() {
		$sms->to('+15555555555');
	});

>The `queue` method will fallback to the `send` method if a queue service is configured within `Laravel.`

#### Pretend

The `pretend` method will simply create a log file that states that a SMS message has been "sent."  This is useful for testing if your configuration settings are working correctly without sending actual messages.

	SMS::pretend('simple-sms::welcome', $data, function() {
		$sms->to('+15555555555');
	});

You may also set the `pretend` configuration option to true to have all SMS messages pretend that they were sent.

	`/app/config/simplesoftwareio/simple-sms/config.php`
	return array(
		'pretend' => true,
	);

#### Receive

###### Coming in Alpha2

Simple SMS will supple Push Messages.

#### Check Messages

###### Coming in Alpha2

Retrieves a list of messages.

#### Get Message

###### Coming in Alpha2

Gets a SMS by its ID.

<a id="message-enclosure"></a>
## Message Enclosure

#### Why Enclosures?

We used enclosures to allow for functions such as the queue method.  Being able to easily save the message enclousure allowed for a much greater flexiabilty in the longer term in return for a slightly more difficult to use package.

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

>The carrier is required for the email driver so that the correct email gateway can be lookedup.  See the table above for a list of accepted carriers.

#### From

The `from` method will set the address from which the message is being sent.

	SMS::send('simple-sms::welcome', $data, function() {
		$sms->from('+15555555555');
	});

#### attachImage

The `attachImage` method will add an image to the message.  This will also convert the message to a MSM because SMS does not support image attachments.

	SMS::send('simple-sms::welcome', $data, function() {
		$sms->attachImage('/path/to/image.jpg');
	});

>Twilio does not currently support attached images.
