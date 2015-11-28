<?php

/**
 * Simple-SMS
 * Simple-SMS is a package made for Laravel to send/receive (polling/pushing) text messages.
 *
 * @link http://www.simplesoftware.io
 * @author SimpleSoftware support@simplesoftware.io
 *
 */
/*
    |--------------------------------------------------------------------------
    | Simple SMS
    |--------------------------------------------------------------------------
    | Driver
    |   Email:  The Email driver uses the Illuminate\Mail\Mailer instance to
    |           send SMS messages based on the carriers e-mail to SMS gateways.
    |           The Email driver will send messages out based on your Laravel
    |           mail settings.
    |   CallFire: https://www.callfire.com/
    |   EzTexting: https://www.eztexting.com/
    |   LabsMobile: http://www.labsmobile.com/
    |   Mozeo: https://www.mozeo.com/
    |   Nexmo: https://www.nexmo.com/
    |   Twilio: https://www.twilio.com/
    |--------------------------------------------------------------------------
    | From
    |   Email:  The from address must be a valid email address.
    |   Twilio: The from address must be a verified phone number within Twilio.
    |--------------------------------------------------------------------------
    | CallFire
    |   App Login:     Your login settings. (https://www.callfire.com/ui/manage/access)
    |   App Password:  Your login password. (https://www.callfire.com/ui/manage/access)
    |--------------------------------------------------------------------------
    | EZTexting Additional Settings
    |   Username:  Your login username.
    |   Password:  Your login password.
    |--------------------------------------------------------------------------
    | LabsMobile
    |   Client:    Your client login. (https://websms.labsmobile.com/SY0204/parameters)
    |   Username:  Your login username.
    |   Password:  Your login password.
    |   Test:      Sends the message as a test if set to true.
    |--------------------------------------------------------------------------
    | Mozeo
    |   Company Key:  Your company key. (https://www.mozeo.com/mozeo/customer/platformdetails.php)
    |   Username:     Your username.  (https://www.mozeo.com/mozeo/customer/platformdetails.php)
    |   Password:     Your password.  (https://www.mozeo.com/mozeo/customer/platformdetails.php)
    |--------------------------------------------------------------------------
    | Nexmo
    |   API Key:     Your API key. (https://dashboard.nexmo.com/private/settings)
    |   API Secret:  Your API secret. (https://dashboard.nexmo.com/private/settings)
    |--------------------------------------------------------------------------
    | Twilio Additional Settings
    |   Account SID:  The Account SID associated with your Twilio account. (https://www.twilio.com/user/account/settings)
    |   Auth Token:   The Auth Token associated with your Twilio account. (https://www.twilio.com/user/account/settings)
    |   Verify:       Ensures extra security by checking if requests
    |                 are really coming from Twilio.
    |--------------------------------------------------------------------------
*/


return [
    'driver' => 'Selected Driver',
    'from' => 'Your Number or Email',
    'callfire' => [
        'app_login' => 'Your CallFire API Login',
        'app_password' => 'Your CallFire API Password'
    ],
    'eztexting' => [
        'username' => 'Your EZTexting Username',
        'password' => 'Your EZTexting Password'
    ],
    'labsmobile' => [
        'client' => 'Your client ID',
        'username' => 'Your Usernbame',
        'password' => 'Your Password',
        'test' => false
    ],
    'mozeo' => [
        'company_key' => 'Your Mozeo Company Key',
        'username' => 'Your Mozeo Username',
        'password' => 'Your Mozeo Password'
    ],
    'nexmo' => [
        'api_key' => 'Your Nexmo api key',
        'api_secret' => 'Your Nexmo api secret'
    ],
    'twilio' => [
        'account_sid' => 'Your SID',
        'auth_token' => 'Your Token',
        'verify' => true,
    ]
];