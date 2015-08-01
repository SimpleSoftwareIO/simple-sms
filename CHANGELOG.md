Simple SMS
==========

##Change Log

####2.0.0
* Updated to Guzzle 6.
* Dropped support for PHP 5.4
* Added the ability to send SMS messages without a view.
* Fixed typos in the read me file.

####2.0.0-Beta1
* Adds support for Laravel 5

####1.1.0
* Added MMS support for Twilio.
* Corrected some typos in the readme.

####1.0.0
* Removed unstable development warnings.
* Basic doc cleanup.

####Beta 1
* Dropping receive support for CallFire due to not being able to get a keyword to test.
* Dropping support for Mozeo receive due to not being able to get an API link forwarding set up automatically.

####Alpha 3
* General comment and code clean up.

####Alpha 2
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