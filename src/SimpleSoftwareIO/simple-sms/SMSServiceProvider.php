<?php namespace SimpleSoftwareIO\Simple-SMS;

use Illuminate\Support\ServiceProvider;
use SimpleSoftwareIO\Simple-SMS\Drivers\DriverInterface;
use SimpleSoftwareIO\Simple-SMS\Drivers\EmailSMS;
use SimpleSoftwareIO\Simple-SMS\Drivers\TwilioSMS;

class SMSServiceProvider extends ServiceProvider {

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
    $this->app->bind('sms', function($app) {

      $sender = $this->registerSender();

      $sms =  new SMS($sender);

      $sms->setContainer($app);
      $sms->setLogger($app['log']);

      //Set the from and pretending settings
      if ($from = $this->app['config']->get('sms::from', false)) $sms->alwaysFrom($from);
      $sms->pretend($this->app['config']->get('sms::pretending', false));

      return $sms;
    });
  }

  /**
   * Register the correct driver based on the config file.
   *
   * @return SimpleSoftwareIO\SMS\Drivers\DriverInterface
   */
  public function registerSender()
  {
    $driver = $this->app['config']->get('sms::driver');

    switch ($driver)
    {
      case 'email':
      return new EmailSMS($this->app['mailer']);

      case 'twilio':
      return new TwilioSMS(new \Services_Twilio($this->app['config']->get('sms::twilio.account_sid'), $this->app['config']->get('sms::twilio.auth_token')));

      default:
      throw new \InvalidArgumentException('Invalid SMS driver.');
    }
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
