<?php

/**
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
    |   Zenvia: http://www.zenvia.com.br/
    |   Plivo:  https://www.plivo.com/
    |--------------------------------------------------------------------------
    | From
    |   Email:  The from address must be a valid email address.
    |   Twilio: The from address must be a verified phone number within Twilio.
    |   Zenvia: Any string, up to 20 chars.
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
    | Zenvia
    |   Account key:    Your account key.
    |   Passcode:       Your code (password) set by the Zenvia Support Team.
    |   callbackOption: It's an API param that sets if Zenvia Servers should or should not send you the status
    |                   of SMS delivery. Valid options are: FINAL => for notification when SMS is delivered,
    |                   ALL => all notifications or NONE => disabled (default). Please, refer to
    |                   http://docs.zenviasms.apiary.io/reference/callbacks-da-api for more info.
    |--------------------------------------------------------------------------
    | Plivo Additional Settings
    |   Auth ID:        The Auth SID associated with your Plivo account.   (https://manage.plivo.com/dashboard/)
    |   Auth Token:     The Auth Token associated with your Plivo account. (https://manage.plivo.com/dashboard/)
    |--------------------------------------------------------------------------
 */

return [
    'driver' => env('SMS_DRIVER','email'),
    
    'from' => env('SMS_FROM','Your Number or Email'),
    
    'callfire' => [
        'app_login' => env('CALLFIRE_LOGIN','Your CallFire API Login'),
        'app_password' => env('CALLFIRE_PASSWORD','Your CallFire API Password'),
    ],

    'eztexting' => [
        'username' => env('EZTEXTING_USERNAME','Your EZTexting Username'),
        'password' => env('EZTEXTING_PASSWORD','Your EZTexting Password'),
    ],

    'labsmobile' => [
        'client' => env('LABSMOBILE_CLIENT_ID','Your client ID'),
        'username' => env('LABSMOBILE_USERNAME','Your Username'),
        'password' => env('LABSMOBILE_PASSWORD','Your Password'),
        'test' => false,
    ],

    'mozeo' => [
        'company_key' => env('MOZEO_COMPANY_KEY','Your Mozeo Company Key'),
        'username' => env('MOZEO_USERNAME','Your Mozeo Username'),
        'password' => env('MOZEO_PASSWORD','Your Mozeo Password'),
    ],

    'nexmo' => [
        'api_key' => env('NEXMO_KEY','Your Nexmo api key'),
        'api_secret' => env('NEXMO_SECRET','Your Nexmo api secret'),
    ],

    'twilio' => [
        'account_sid' => env('TWILIO_SID','Your SID'),
        'auth_token' => env('TWILIO_TOKEN','Your Token'),
        'verify' => true,
    ],

    'zenvia' => [
        'account_key' => env('ZENVIA_KEY','Your Zenvia account key'),
        'passcode' => env('ZENVIA_PASSCODE','Your code (password)'),
        'callbackOption' => 'NONE',
    ],

    'plivo' => [
        'authId' => env('PLIVO_AUTH_ID','Your Plivo Auth Id'),
        'authToken' => env('PLIVO_AUTH_TOKEN','Your Plivo Auth Token')
    ],
];
