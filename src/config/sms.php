<?php

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
    'infobip'=> [
         'username' => 'username of infobip',
         'password' => 'password of infobip'
    ],
    'plivo' => [
        'authId' => env('PLIVO_AUTH_ID','Your Plivo Auth Id'),
        'authToken' => env('PLIVO_AUTH_TOKEN','Your Plivo Auth Token')
    ],
    'flowroute' => [
        'access_key' => 'Your Flowroute access key',
        'secret_key' => 'Your Flowroute secret key'
    ],
];
