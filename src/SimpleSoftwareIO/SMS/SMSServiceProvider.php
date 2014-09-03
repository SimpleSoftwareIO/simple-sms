<?php namespace SimpleSoftwareIO\SMS;

/**
 * Simple-SMS
 * Simple-SMS is a package made for Laravel to send/receive (polling/pushing) text messages.
 *
 * @link http://www.simplesoftware.io
 * @author SimpleSoftware support@simplesoftware.io
 *
 */

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use SimpleSoftwareIO\SMS\Drivers\CallFireSMS;
use SimpleSoftwareIO\SMS\Drivers\EmailSMS;
use SimpleSoftwareIO\SMS\Drivers\EZTextingSMS;
use SimpleSoftwareIO\SMS\Drivers\MozeoSMS;
use SimpleSoftwareIO\SMS\Drivers\TwilioSMS;

class SMSServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('simplesoftwareio/simple-sms');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('sms', function ($app) {

            $sender = $this->registerSender();

            $sms = new SMS($sender);

            $sms->setContainer($app);
            $sms->setLogger($app['log']);
            $sms->setQueue($app['queue']);

            //Set the from and pretending settings
            if ($from = $this->app['config']->get('simple-sms::from', false)) $sms->alwaysFrom($from);
            $sms->setPretending($this->app['config']->get('simple-sms::pretend', false));

            return $sms;
        });
    }

    /**
     * Register the correct driver based on the config file.
     *
     * @return CallFireSMS|EmailSMS|EZTextingSMS|MozeoSMS|TwilioSMS
     * @throws \InvalidArgumentException
     */
    public function registerSender()
    {
        $driver = $this->app['config']->get('simple-sms::driver');

        switch ($driver) {
            case 'email':
                return new EmailSMS($this->app['mailer']);

            case 'twilio':
                return $this->buildTwilio();

            case 'eztexting':
                return $this->buildEZTexting();

            case 'callfire':
                return $this->buildCallFire();

            case 'mozeo':
                return $this->buildMozeo();

            default:
                throw new \InvalidArgumentException('Invalid SMS driver.');
        }
    }

    protected function buildTwilio()
    {
        return new TwilioSMS(
            new \Services_Twilio(
                $this->app['config']->get('simple-sms::twilio.account_sid'),
                $this->app['config']->get('simple-sms::twilio.auth_token')
            ),
            $this->app['config']->get('simple-sms::twilio.auth_token'),
            $this->app['request']->url(),
            $this->app['config']->get('simple-sms::twilio.verify')
        );
    }

    protected function buildEZTexting()
    {
        $provider = new EZTextingSMS(new Client);

        $data = [
            'User' => $this->app['config']->get('simple-sms::eztexting.username'),
            'Password' => $this->app['config']->get('simple-sms::eztexting.password')
        ];

        $provider->buildBody($data);

        return $provider;
    }

    protected function buildCallFire()
    {
        $provider = new CallFireSMS(new Client);

        $provider->setUser($this->app['config']->get('simple-sms::callfire.app_login'));
        $provider->setPassword($this->app['config']->get('simple-sms::callfire.app_password'));

        return $provider;
    }

    protected function buildMozeo()
    {
        $provider = new MozeoSMS(new Client);

        $auth = [
            'companykey' => $this->app['config']->get('simple-sms::mozeo.companyKey'),
            'username' => $this->app['config']->get('simple-sms::mozeo.username'),
            'password' => $this->app['config']->get('simple-sms::mozeo.password'),
        ];

        $provider->buildBody($auth);

        return $provider;
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('sms', 'emailsms', 'twiliosms');
    }

}
