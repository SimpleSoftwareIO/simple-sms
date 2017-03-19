Simple SMS
==========

##Change Log

#### 3.1.1
* Laravel 5.4 Fix
* Added US Cellular

#### 3.1.0
* Added Just Send Driver and Bug Fixes

#### 3.0.1
* Fixed a bug where the config file was not being copied correctly.

#### 3.0.0
* Updated Readme

#### 3.0.0-Beta1
* Added FlowRoute Driver. --Thanks [rasumo](https://github.com/rasumo)
* Added Plivo Driver. --Thanks [rasumo](https://github.com/rasumo)
* Config file now users env by default. --Thanks [rasumo](https://github.com/rasumo)
* Config file now users env by default. --Thanks [rasumo](https://github.com/rasumo)
* Added a Log driver.
* Removed the pretending methods.  Use the Log driver instead.
* Fixed a bug where sending more than one SMS message would result in an incorrectly built URL path.
* Refactor much of the package to use traits.
* A SMSNotSentException is now thrown in favor of a general Exception. --Thanks [cozylife](https://github.com/cozylife)
* Moved to PSR-4 Autoloading.
* PSR-2 Cleanup.

#### 2.1.2
* An `outgoing message` is now returned when a message is sent. --Thanks [marfurt](https://github.com/marfurt)
* The E-Mail driver only sends emails in text format now.  --Thanks [cozylife](https://github.com/cozylife)
* Added a new Zenvia Driver.  --Thanks [filipegar](https://github.com/filipegar)
* Updated docs to point to simplesoftware.io

#### 2.1.1
* Updated Twilio dependency.

#### 2.1.0
* Fixed doc blocks --Thanks [Ellrion](https://github.com/Ellrion)
* Created Driver Manager class to better manager drivers. --Thanks [Ellrion](https://github.com/Ellrion)
* Added LabsMobile driver --Thanks [borislalov](https://github.com/borislalov)
* Added Nexmo driver --Thanks [cricu](https://github.com/cricu)
* Added ability to switch drivers at runtime.
* Fixed a bug when the `queue` method is called upon.

#### 2.0.0
* Full Laravel 5.X support.
* Updated to Guzzle 6.
* Dropped support for PHP 5.4
* Added the ability to send SMS messages without a view.
* Fixed typos in the read me file.

####2.0.0-Beta1
* Adds support for Laravel 5

#### 1.1.0
* Added MMS support for Twilio.
* Corrected some typos in the readme.

#### 1.0.0
* Removed unstable development warnings.
* Basic doc cleanup.

#### Beta 1
* Dropping receive support for CallFire due to not being able to get a keyword to test.
* Dropping support for Mozeo receive due to not being able to get an API link forwarding set up automatically.

#### Alpha 3
* General comment and code clean up.

#### Alpha 2
* Expanded documentation.
* EZTexting now supports checking for messages.
* CallFire now supports checking for messages.
* Added error detection on API calls.
* Push SMS Messages now work with EZTexting.
* Push SMS messages now work with Twilio.
* `SMS::queue` now works.
* Added [EZTexting Driver](https://www.eztexting.com/)
* Added [CallFire Driver](https://www.callfire.com/)
* Added [Mozeo Driver](https://www.mozeo.com/)
* Fixed a bug where the `pretend` configuration variable was not working.
* The E-Mail Driver will now throw an error if a carrier is not found or provided.
* `SMS::pretend()` now works as documented.
