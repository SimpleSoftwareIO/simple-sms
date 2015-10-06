<?php namespace SimpleSoftwareIO\SMS;

/**
 * Simple-SMS
 * Simple-SMS is a package made for Laravel to send/receive (polling/pushing) text messages.
 *
 * @link http://www.simplesoftware.io
 * @author SimpleSoftware support@simplesoftware.io
 *
 */

use Illuminate\Support\ServiceProvider;

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
        $this->publishes([
            __DIR__.'/../../config/sms.php' => config_path('sms.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sms', function ($app) {

            $this->registerSender();

            $sms = new SMS($app['sms.sender']);

            $this->setSMSDependencies($sms, $app);

            //Set the from and pretending settings
            if ($app['config']->has('sms.from')) {
                $sms->alwaysFrom($app['config']['sms']['form']);
            }
            $sms->setPretending($app['config']->get('sms.pretend', false));

            return $sms;
        });
    }

    /**
     * Register the correct driver based on the config file.
     *
     * @return void
     */
    public function registerSender()
    {
        $this->app['sms.sender'] = $this->app->share(function ($app) {
            return (new DriverManager($app))->driver();
        });
    }

    /**
     * Set a few dependencies on the sms instance.
     *
     * @param SMS $sms
     * @param  $app
     * @return void
     */
    private function setSMSDependencies($sms, $app)
    {
        $sms->setContainer($app);
        $sms->setLogger($app['log']);
        $sms->setQueue($app['queue']);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('sms', 'sms.sender');
    }
}
