<?php

namespace SimpleSoftwareIO\SMS;

use GuzzleHttp\Client;
use Illuminate\Support\Manager;
use SimpleSoftwareIO\SMS\Drivers\EmailSMS;
use SimpleSoftwareIO\SMS\Drivers\EZTextingSMS;
use SimpleSoftwareIO\SMS\Drivers\InfobipSMS;
use SimpleSoftwareIO\SMS\Drivers\LabsMobileSMS;
use SimpleSoftwareIO\SMS\Drivers\MozeoSMS;
use SimpleSoftwareIO\SMS\Drivers\NexmoSMS;
use SimpleSoftwareIO\SMS\Drivers\TwilioSMS;
use SimpleSoftwareIO\SMS\Drivers\ZenviaSMS;
use SimpleSoftwareIO\SMS\Drivers\CallFireSMS;
use SimpleSoftwareIO\SMS\Drivers\PlivoSMS;
use SimpleSoftwareIO\SMS\Drivers\FlowrouteSMS;

class DriverManager extends Manager
{
    /**
     * Get the default sms driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['sms.driver'];
    }

    /**
     * Set the default sms driver name.
     *
     * @param string $name
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['sms.driver'] = $name;
    }

    /**
     * Create an instance of the callfire driver.
     *
     * @return CallFireSMS
     */
    protected function createCallfireDriver()
    {
        $config = $this->app['config']->get('sms.callfire', []);

        $provider = new CallFireSMS(new Client());

        $provider->setUser($config['app_login']);
        $provider->setPassword($config['app_password']);

        return $provider;
    }

    /**
     * Creates an instance of the email driver.
     *
     * @return EmailSMS
     */
    protected function createEmailDriver()
    {
        $provider = new EmailSMS($this->app['mailer']);

        return $provider;
    }

    /**
     * Create an instance of the eztexting driver.
     *
     * @return EZTextingSMS
     */
    protected function createEztextingDriver()
    {
        $config = $this->app['config']->get('sms.eztexting', []);

        $provider = new EZTextingSMS(new Client());

        $data = [
            'User' => $config['username'],
            'Password' => $config['password'],
        ];
        $provider->buildBody($data);

        return $provider;
    }

    protected function createLabsMobileDriver()
    {
        $config = $this->app['config']->get('sms.labsmobile', []);

        $provider = new LabsMobileSMS(new Client());

        $auth = [
            'client' => $config['client'],
            'username' => $config['username'],
            'password' => $config['password'],
            'test' => $config['test'],
        ];

        $provider->buildBody($auth);

        return $provider;
    }

    /**
     * Create an instance of the mozeo driver.
     *
     * @return MozeoSMS
     */
    protected function createMozeoDriver()
    {
        $config = $this->app['config']->get('sms.mozeo', []);

        $provider = new MozeoSMS(new Client());

        $auth = [
            'companykey' => $config['company_key'],
            'username' => $config['username'],
            'password' => $config['password'],
        ];
        $provider->buildBody($auth);

        return $provider;
    }

    /**
     * Create an instance of the nexmo driver.
     *
     * @return MozeoSMS
     */
    protected function createNexmoDriver()
    {
        $config = $this->app['config']->get('sms.nexmo', []);

        $provider = new NexmoSMS(
            new Client(),
            $config['api_key'],
            $config['api_secret']
        );

        return $provider;
    }

    /**
     * Create an instance of the Twillo driver.
     *
     * @return TwilioSMS
     */
    protected function createTwilioDriver()
    {
        $config = $this->app['config']->get('sms.twilio', []);

        return new TwilioSMS(
            new \Services_Twilio($config['account_sid'], $config['auth_token']),
            $config['auth_token'],
            $this->app['request']->url(),
            $config['verify']
        );
    }

    /**
     * Create an instance of the Zenvia driver.
     *
     * @return ZenviaSMS
     */
     protected function createZenviaDriver()
     {
         $config = $this->app['config']->get('sms.zenvia', []);
         $provider = new ZenviaSMS(
             new Client(),
             $config['account_key'],
             $config['passcode'],
             $config['callbackOption']
         );
         return $provider;
     }

    /**
     * Create an instance of the InfoBip driver.
     *
     * @return InfoBip
     */
    protected function createInfobipDriver()
    {
        $config = $this->app['config']->get('sms.infobip', []);
        $provider = new InfobipSMS(new \infobip\SmsClient($config['username'], $config['password']));

        return $provider;
    }

    /**
     * Create an instance of the Plivo driver.
     *
     * @return PlivoSMS
     */
    protected function createPlivoDriver()
    {
        $config = $this->app['config']->get('sms.plivo', []);

        $provider = new PlivoSMS(
            $config['authId'],
            $config['authToken']
        );

        return $provider;
    }

    /**
     * Create an instance of the flowroute driver
     *
     * @return FlowrouteSMS
     */
    protected function createFlowrouteDriver()
    {
        $config = $this->app['config']->get('sms.flowroute', []);

        $provider = new FlowrouteSMS(
            new Client,
            $config['access_key'],
            $config['secret_key']
        );

        return $provider;
    }
}
