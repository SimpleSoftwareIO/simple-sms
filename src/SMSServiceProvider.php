<?php

namespace SimpleSoftwareIO\SMS;

use Illuminate\Support\ServiceProvider;
use SimpleSoftwareIO\SMS\NotificationChannel\SMSChannel;

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
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/sms.php' => config_path('sms.php'),
        ]);

        $this->app->when(SMSChannel::class)
            ->needs(SMS::class)
            ->give(function () {
                return $this->app->make('sms');
            });
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('sms', function ($app) {
            $this->registerSender();
            $sms = new SMS($app['sms.sender']);
            $this->setSMSDependencies($sms, $app);

            //Set the from setting
            if ($app['config']->has('sms.from')) {
                $sms->alwaysFrom($app['config']['sms']['from']);
            }

            return $sms;
        });
    }

    /**
     * Register the correct driver based on the config file.
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
     */
    private function setSMSDependencies($sms, $app)
    {
        $sms->setContainer($app);
        $sms->setQueue($app['queue']);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sms', 'sms.sender'];
    }
}
