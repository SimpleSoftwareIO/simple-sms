Simple SMS
==========

##Change Log

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